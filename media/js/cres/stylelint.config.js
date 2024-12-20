module.exports = {
    overrides: [
        {
            files: ['*.scss'],
            customSyntax: 'postcss-scss'
        }
    ],
    rules: {
        'at-rule-no-unknown': [
            true,
            {
                ignoreAtRules: [
                    'tailwind',
                    'apply',
                    'variants',
                    'responsive',
                    'screen'
                ]
            }
        ],
        'declaration-block-trailing-semicolon': null,
        'no-descending-specificity': null
    }
};
