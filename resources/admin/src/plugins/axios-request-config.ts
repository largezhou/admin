/**
 * IDE 辅助
 */
export interface AxiosRequestConfig {
    /**
     * 是否以 message 的方式显示第一条 422 错误消息
     * @default false
     */
    showValidationMsg?: Boolean,
    /**
     * 请求时的表单所在的组件，配合 error key 使用，可以自动填充表单中的 errors 错误提示
     * @default null
     */
    validationForm?: any,
    /**
     * 页面中，存储错误的键，默认为 errors
     * @default 'errors'
     */
    validationErrorKey?: String,
    /**
     * 请求出现 401 时，默认会打开弹框要求登录，设置 true 来禁止
     * @default false
     */
    disableLoginDialog?: Boolean,
}
