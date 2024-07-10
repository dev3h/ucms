const baseRuleValidate = ($t) => ([
    { required: true, message: $t('validate.required'), trigger: ['blur', 'change'] }
]);

export default baseRuleValidate;
