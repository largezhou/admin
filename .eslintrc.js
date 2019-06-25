module.exports = {
  root: true,
  env: {
    node: true,
  },
  'extends': [
    'plugin:vue/strongly-recommended',
    '@vue/standard',
  ],
  rules: {
    'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    'comma-dangle': ['error', 'always-multiline'],
    'space-before-function-paren': [
      'error',
      {
        anonymous: 'always',
        named: 'never',
        asyncArrow: 'always',
      },
    ],
    'eqeqeq': ['warn'],
    'no-eval': ['warn'],
    'vue/require-component-is': 'never',
    'vue/no-parsing-error': [
      2,
      {
        'x-invalid-end-tag': false,
      },
    ],
    'vue/max-attributes-per-line': [
      'error', {
        'singleline': 3,
        'multiline': {
          'max': 1,
          'allowFirstLine': false,
        },
      },
    ],
    'vue/html-closing-bracket-spacing': [
      'error',
      {
        'selfClosingTag': 'never',
      },
    ],
    'vue/singleline-html-element-content-newline': 'off',
    'vue/require-default-prop': 'off',
  },
  parserOptions: {
    parser: 'babel-eslint',
  },
}
