// Matches dashed string for camelizing
const rdashAlpha = /-([a-z])/g;

// Used by camelCase as callback to replace()
const fcamelCase = (_all, letter) => {
    return letter.toUpperCase();
};

// Convert dashed to camelCase
const camelCase = (string) => {
    return string.replace(rdashAlpha, fcamelCase);
};

export default camelCase;
