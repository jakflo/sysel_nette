<?php
namespace App\UI\OrderDetail;

use \App\UI\Exceptions\NotFoundException;
use \App\UI\Tools\ProtectedIn;
use \App\UI\OrderDetail\OrderDetailException;
use \Doctrine\DBAL\Types\Type;
use \App\UI\Entities\OrderStatus;
use \App\UI\Entities\Orders;
use \Doctrine\ORM\Query;

class OrderDetailModel
{
    public function __construct(
            protected \Doctrine\ORM\EntityManager $em, 
            protected \App\UI\Model\Database $db, 
            protected \App\UI\FindItems\ItemsQueryFactory $items_query_factory, 
            protected \App\UI\FindItems\FindItemsModelFactory $find_items_model_factory, 
            protected \App\UI\ItemsInWarehouse\ItemsInWarehouseModelFactory $items_in_warehouse_model_factory, 
            protected \App\UI\Orders\OrdersModelFactory $orders_model_factory, 
            protected int $order_id
    )
    {
        
    }
    
    public function getOrderDetails(): array
    {
        $return = $this->em->createQuery(
                "SELECT o.id, o.added, o.last_edited, o.note, o.client_id, s.id AS status_id, s.short_name AS status_shortname 
                FROM App\\UI\\Entities\\Orders o 
                JOIN o.status s 
                WHERE o.id = :oid"
        )
                ->setParameter('oid', $this->order_id)
                ->getOneOrNullResult()
                ;
        
        if (!$return) {
            throw new NotFoundException('Objednavka nenalezena', NotFoundException::ORDER);
        }
        
        return $return;
    }
    
    public function getClientInfo(): array
    {
        $order_details = $this->getOrderDetails();
        
        $return = $this->em->createQuery(
                "SELECT c.title, c.forname, c.surname, c.middlename, c.email, c.phone, a.street, a.city, a.zip, a.country 
                FROM App\\UI\\Entities\\Client c 
                JOIN c.address a 
                WHERE c.id = :cid"
        )
                ->setParameter('cid', $order_details['client_id'])
                ->getOneOrNullResult()
                ;
        
        if (!$return) {
            throw new NotFoundException('Klient nebo adresa nenalezeny');
        }
        
        $full_name = [];
        if (!empty($return['title'])) {
            $full_name[] = $return['title'];
        }
        $full_name[] = $return['forname'];
        if (!empty($return['middlename'])) {
            $full_name[] = $return['middlename'];
        }
        $full_name[] = $return['surname'];
        $return['fullname'] = implode(' ', $full_name);
        
        return $return;
    }
    
    public function getItemsInOrder(): array
    {
        $this->getOrderDetails();
        return $this->em->createQuery(
                "SELECT it.id AS item_id, it.name AS item_name, ohi.amount AS item_amount
                FROM App\\UI\\Entities\\OrderHasItem ohi 
                JOIN ohi.item it 
                WHERE ohi.order_id = :oid"
        )
                ->setParameter('oid', $this->order_id)
                ->getResult()
                ;
    }
    
    public function getItemsQuery(): \App\UI\FindItems\ItemsQuery
    {
        $items_in_order = $this->getItemsInOrder();
        $items_query = $this->items_query_factory->create(null, $items_in_order);
        return $items_query;
    }
    
    public function getAllowedStatusChanges(string $status_shortname): array
    {
        $allowed_status_changes = [
            'new' => [
                'items_reserved', 
                'storno'
            ], 
            'items_reserved' => [
                'new', 
                'sent_off', 
                'storno'
            ], 
            'sent_off' => [
                'items_reserved', 
                'complain_in_progress'
            ], 
            'complain_in_progress' => [
                'sent_off', 
                'items_returned'
            ], 
            'items_returned' => [
                'complain_in_progress'
            ], 
            'storno' => [
                'new'
            ]
        ];
        
        if (!isset($allowed_status_changes[$status_shortname])) {
            throw new \Exception('Neznamy status objednavky');
        }
        
        $allowed_status_changes_for_item = $allowed_status_changes[$status_shortname];
        return $allowed_status_changes_for_item;
    }
    
    public function getAllowedStatusesForSelect(): array
    {
        $order_details = $this->getOrderDetails();
        $current_status = $order_details['status_shortname'];
        $allowed_statuses_shortname = $this->getAllowedStatusChanges($current_status);
        array_unshift($allowed_statuses_shortname, $order_details['status_shortname']);
        
        $statuses = new ProtectedIn();
        $statuses->addArray('sns', $allowed_statuses_shortname);
        
        return $this->db->fetchPairs(
                "SELECT short_name, name FROM order_status WHERE short_name IN({$statuses->getTokens('sns')}) ORDER BY id", 
                $statuses->getData()
                );
    }
    
    /**
     * prideli polozky objednavce se stavem Nova. Jejdrive bude brat polozky ze skladu vybranych v $prefered_warehouses_id, 
     * pak ze skladu dle jejich zaplnenosti. Pote zmeni stav objednavky na Pripraveno k odeslani 
     * @param array $prefered_warehouses_id
     * @throws OrderDetailException
     */
    public function assignItemsToOrder(array $prefered_warehouses_id = [])
    {
        $order_detail = $this->getOrderDetails();
        if ($order_detail['status_shortname'] !== 'new') {
            throw new OrderDetailException('Objednavka uz neni nova', OrderDetailException::ORDERISNOTNEW);
        }
        
        $items_query = $this->getItemsQuery();
        if (count($items_query->getItemsNotFound()) > 0) {
            throw new OrderDetailException('Vsechny polozky objednavky nebyly nalezeny', OrderDetailException::NOTALLITEMSFOUND);
        }
        
        if (count($prefered_warehouses_id) > 0) {
            $find_items_model = $this->find_items_model_factory->create();
            $find_items_model->checkWarehousesExist($prefered_warehouses_id);
            $prefered_warehouses_in = new ProtectedIn();
            $prefered_warehouses_in->addArray('wid', $prefered_warehouses_id);
            $prefered_warehouses_term = "AND id IN ({$prefered_warehouses_in->getTokens('wid')})";
            $prefered_warehouses_params = $prefered_warehouses_in->getData();
        } else {
            $prefered_warehouses_term = '';
            $prefered_warehouses_params = [];
        }
        
        $this->db->executeQuery(
                "CREATE TEMPORARY TABLE wa AS 
                SELECT wi.warehouse_id, (SUM(i.area) / w.area) AS area_filled
                FROM warehouse_has_item wi 
                JOIN warehouse w ON wi.warehouse_id = w.id 
                JOIN item_with_lot il ON wi.item_with_lot_id = il.id 
                JOIN item i ON il.item_id = i.id 
                JOIN item_status it ON wi.status_id = it.id
                WHERE it.short_name IN ('available', 'reserved')
                GROUP BY wi.warehouse_id"
        );
        
        foreach ($this->getItemsInOrder() as $order_item) {
            $found_items = $this->db->fetchFirstColumn(
                    "SELECT whi.id, 
                    CASE 
                        WHEN ws.id IS NULL THEN 0 
                        ELSE 1 
                    END AS wp, w.id AS wid, waj.area_filled 
                    FROM warehouse_has_item whi 
                    JOIN item_with_lot iwl ON whi.item_with_lot_id = iwl.id  
                    JOIN item it ON iwl.item_id = it.id 
                    JOIN item_status ist ON whi.status_id = ist.id 
                    JOIN warehouse w ON whi.warehouse_id = w.id 
                    LEFT JOIN (
                        SELECT id FROM warehouse WHERE 1 {$prefered_warehouses_term}
                    ) AS ws ON whi.warehouse_id = ws.id 
                    JOIN 
                    (
                        SELECT * FROM wa 
                    ) AS waj ON waj.warehouse_id = w.id
                    WHERE ist.short_name = 'available' 
                    AND it.id = :itid 
                    ORDER BY wp DESC, waj.area_filled DESC, whi.id 
                    LIMIT :am", 
                    array_merge(
                        [
                            'itid' => $order_item['item_id'], 
                            'am' => $order_item['item_amount']
                        ], 
                        $prefered_warehouses_params                                        
                    ), 
                    ['am' => Type::getType('integer')]
            );
            $this->items_in_warehouse_model_factory->create()->changeItemsStatuses('reserved', $this->order_id, $found_items);
        }
        
        $this->db->executeQuery("DROP TEMPORARY TABLE wa");
        $this->changeOrderStatus('items_reserved');
    }
    
    public function changeOrderStatus(string $order_status_short_name)
    {
        $items_in_warehouse_model = $this->items_in_warehouse_model_factory->create();
        $order_detail = $this->getOrderDetails();
        $order_status = $this->em->getRepository(OrderStatus::class)->findOneBy(['short_name' => $order_status_short_name]);
        
        $warehouse_has_item_id_in_order = $this->em
                ->createQuery("SELECT iw.id FROM App\\UI\Entities\\WarehouseHasItem iw WHERE iw.order_id = :oid")
                ->setParameter('oid', $this->order_id)
                ->getResult(Query::HYDRATE_SCALAR_COLUMN)
                ;
      
        if (!$order_status) {
            throw new OrderDetailException('Neznamy stav objednavky');
        }
        
        if (!in_array($order_status_short_name, $this->getAllowedStatusChanges($order_detail['status_shortname']))) {
            throw new OrderDetailException('Nepovoleny typ zmeny stavu objednavky', OrderDetailException::INVALIDSTATUSCHANGE);
        }
        
        if (
                $order_status_short_name === 'items_reserved' && 
                ($order_detail['status_shortname'] === 'new' || $order_detail['status_shortname'] === 'storno') && 
                count($warehouse_has_item_id_in_order) === 0
        ) {
            throw new OrderDetailException(
                'Pred zmenou stavu objednavky na Pripraveno k odelsani nutno pridelit polozky k objednavce', 
                OrderDetailException::ORDERSITEMSMUSTBEASSIGNED
            );
        }
        
        $order = $this->orders_model_factory->create()->getOrder($this->order_id);
        $order->setStatus($order_status)
                ->setLastEdited(new \DateTime())
                ;
        $this->em->flush();
        
        //nakonec zmenime stav polozek v objednavce
        if (
                $order_detail['status_shortname'] === 'items_reserved' && 
                ($order_status_short_name === 'new' || $order_status_short_name === 'storno')                
                ) {
            $items_in_warehouse_model->changeItemsStatuses('available', null, $warehouse_has_item_id_in_order);
        } elseif ($order_status_short_name === 'items_reserved' && $order_detail['status_shortname'] === 'sent_off') {
            $items_in_warehouse_model->changeItemsStatuses('reserved', $this->order_id, $warehouse_has_item_id_in_order);
        } elseif ($order_status_short_name === 'sent_off') {
            $items_in_warehouse_model->changeItemsStatuses('sent_off', $this->order_id, $warehouse_has_item_id_in_order);
        } elseif ($order_status_short_name === 'items_returned') {
            $items_in_warehouse_model->changeItemsStatuses('returned', $this->order_id, $warehouse_has_item_id_in_order);
        } elseif ($order_status_short_name === 'complain_in_progress' && $order_detail['status_shortname'] === 'items_returned') {
            $items_in_warehouse_model->changeItemsStatuses('sent_off', $this->order_id, $warehouse_has_item_id_in_order);
        }
    }
    
    public function getOrderId(): int
    {
        return $this->order_id;
    }
    
}
