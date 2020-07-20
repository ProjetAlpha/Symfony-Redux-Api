export const normalizeData = (data, normalizedKeys)  => {
    let normalizedObject = {};

    for (const [key, value] of Object.entries(data)) {
        if (normalizedKeys[key]) {
            normalizedObject[normalizedKeys[key]] = value;
        }
    }

    return normalizedObject;
}