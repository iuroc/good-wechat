"use strict";
exports.__esModule = true;
/**
 * 简单的 Ajax 库
 */
var Ajax = /** @class */ (function () {
    function Ajax(_a) {
        var _b = _a.url, url = _b === void 0 ? '' : _b, _c = _a.method, method = _c === void 0 ? 'GET' : _c, _d = _a.headers, headers = _d === void 0 ? {} : _d, _e = _a.content, content = _e === void 0 ? '' : _e, _f = _a.success, success = _f === void 0 ? function (_responseText, _xhr) { } : _f;
        var xhr = new XMLHttpRequest();
        xhr.open(method, url);
        var name;
        for (name in headers) {
            xhr.setRequestHeader(name, headers[name]);
        }
        xhr.send(content);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                success(xhr.responseText, xhr);
            }
        };
    }
    return Ajax;
}());
exports["default"] = Ajax;
