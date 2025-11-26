type listType = {
    id: string;
    name: string;
};

function arrayToList(input: Record<string, string>): listType[] {
    let k;
    const output: listType[] = [];

    for (k in input) {
        output.push({
            id: k,
            name: input[k]
        });
    }

    return output;
}

function safeJsonStringify<T>(input: T): string {
    return JSON.stringify(input)
        .replace(/</g, "\\u003C")
        .replace(/>/g, "\\u003E")
        .replace(/&/g, "\\u0026")
        .replace(/\u2028/g, "\\u2028")
        .replace(/\u2029/g, "\\u2029")
    ;
}

export { arrayToList, safeJsonStringify };
export type { listType };