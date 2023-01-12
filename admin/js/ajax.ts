/**
 * 简单的 Ajax 库
 */
class Ajax {
    constructor({
        url = '',
        method = 'GET',
        headers = {},
        content = '',
        success = function (_responseText?: string, _xhr?: XMLHttpRequest) { }
    }: AjaxConfig) {
        const xhr = new XMLHttpRequest()
        xhr.open(method, url)
        let name: string
        for (name in headers) {
            xhr.setRequestHeader(name, headers[name])
        }
        xhr.send(content)
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                success(xhr.responseText, xhr)
            }
        }
    }
}
interface AjaxConfig {
    url?: string,
    method?: string,
    headers?: {
        [key: string]: string
    },
    content?: string,
    success?: (responseText?: string, xhr?: XMLHttpRequest) => void
}
export default Ajax