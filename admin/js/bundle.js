(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
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

},{}],2:[function(require,module,exports){
"use strict";
exports.__esModule = true;
var ajax_1 = require("./ajax");
var poncon_1 = require("./poncon");
var poncon = new poncon_1["default"]();
poncon.setPageList(['home', 'about']);
var pageData = {
    home: {
        load: false,
        loadData: function (dom) {
            new ajax_1["default"]({
                url: 'get_keyword_list.php',
                success: function (data) {
                    pageData.home.load = true;
                    var ele_tbody = dom.querySelector('tbody');
                    var ele_table = dom.querySelector('table');
                    if (ele_table && ele_tbody && data) {
                        ele_tbody.innerHTML = data;
                    }
                    ele_table.style.display = 'revert';
                    var eles = dom.querySelectorAll('td[contenteditable]');
                    eles.forEach(function (element) {
                        element.addEventListener('keydown', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                event.preventDefault();
                            }
                        });
                        element.addEventListener('keyup', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                update(event);
                            }
                        });
                    });
                    var eles_delete = dom.querySelectorAll('td.delete');
                    eles_delete.forEach(function (element) {
                        element.addEventListener('click', function (event) {
                            var ele_self = event.target;
                            var ele_parent = ele_self.parentNode;
                            var id = ele_self.getAttribute('data-id');
                            new ajax_1["default"]({
                                url: 'delete_keyword.php',
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                content: 'id=' + id,
                                success: function (data) {
                                    if (!data) {
                                        alert('删除失败');
                                    }
                                    ele_parent.remove();
                                }
                            });
                        });
                    });
                }
            });
            var ele_addKeyword = dom.querySelector('.addKeyword');
            ele_addKeyword.addEventListener('click', function () {
                var _a, _b;
                var ele_firstLineDelete = dom.querySelector('.delete');
                if (ele_firstLineDelete && !ele_firstLineDelete.getAttribute('data-id')) {
                    var parentNode2 = ele_firstLineDelete.parentNode;
                    (_a = parentNode2 === null || parentNode2 === void 0 ? void 0 : parentNode2.querySelector('td')) === null || _a === void 0 ? void 0 : _a.focus();
                    return;
                }
                var ele_tbody = dom.querySelector('tbody');
                var newEle = document.createElement('tr');
                newEle.innerHTML = "\n                <td contenteditable=\"true\" class=\"keyword\"></td>\n                <td contenteditable=\"true\" class=\"reply\"></td>\n                <td class=\"text-nowrap text-danger user-select-none delete\" role=\"button\">\u5220\u9664</td>";
                ele_tbody.insertBefore(newEle, ele_tbody.children[0]);
                var eles = dom.querySelectorAll('td[contenteditable]');
                eles.forEach(function (element) {
                    element.addEventListener('keydown', function (event) {
                        if (event.keyCode == 13 && !event.shiftKey) {
                            event.preventDefault();
                        }
                    });
                    element.addEventListener('keyup', function (event) {
                        if (event.keyCode == 13 && !event.shiftKey) {
                            update(event);
                        }
                    });
                });
                (_b = newEle.querySelector('td')) === null || _b === void 0 ? void 0 : _b.focus();
            });
        }
    }
};
poncon.setPage('home', function (target, dom) {
    if (!pageData.home.load) {
        pageData.home.loadData(dom);
    }
});
poncon.start();
/**
 * 关键词回复列表，保存记录
 */
function update(event) {
    var _a, _b;
    var ele_self = event.target;
    var id = ele_self.getAttribute('data-id');
    var ele_keyword = (_a = ele_self.parentElement) === null || _a === void 0 ? void 0 : _a.querySelector('td.keyword');
    var ele_reply = (_b = ele_self.parentElement) === null || _b === void 0 ? void 0 : _b.querySelector('td.reply');
    var keyword = ele_keyword === null || ele_keyword === void 0 ? void 0 : ele_keyword.innerText;
    var reply = ele_reply === null || ele_reply === void 0 ? void 0 : ele_reply.innerText;
    new ajax_1["default"]({
        url: 'update_keyword.php',
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        content: 'keyword=' + encodeURIComponent(keyword)
            + '&reply=' + encodeURIComponent(reply)
            + '&id=' + id,
        success: function (data) {
            if (!data) {
                alert('修改失败');
            }
            else {
                location.reload();
            }
        }
    });
}

},{"./ajax":1,"./poncon":3}],3:[function(require,module,exports){
"use strict";
exports.__esModule = true;
/**
 * PonconJS 前端路由控制系统
 * @author 欧阳鹏
 * https://apee.top
 */
var Poncon = /** @class */ (function () {
    function Poncon() {
        this.pages = {};
        this.pageNames = []; // 页面列表
    }
    /**
     * 切换页面显示
     * @param target 页面标识
     */
    Poncon.prototype.changeView = function (target) {
        if (!target) {
            return;
        }
        document.querySelectorAll('.poncon-page').forEach(function (dom) {
            dom.style.display = 'none';
        });
        var dom = document.querySelector(".poncon-".concat(target));
        dom.style.display = '';
    };
    /**
     * 设置页面名称列表
     * @param pageNames 页面名称列表
     */
    Poncon.prototype.setPageList = function (pageNames) {
        var _this_1 = this;
        pageNames.forEach(function (target) {
            var dom = document.querySelector(".poncon-".concat(target));
            _this_1.pages[target] = {
                dom: dom,
                event: (function () { })
            };
        });
        this.pageNames = pageNames;
    };
    /**
     * 配置页面
     * @param target 页面标识
     * @param func 页面载入事件
     */
    Poncon.prototype.setPage = function (target, func) {
        if (!target) {
            return;
        }
        var dom = document.querySelector(".poncon-".concat(target));
        this.pages[target] = {
            dom: dom,
            event: func || (function () { })
        };
    };
    /**
     * 开启路由系统
     */
    Poncon.prototype.start = function () {
        var _this = this;
        window.addEventListener('hashchange', function (event) {
            var hash = new URL(event.newURL).hash;
            _this.loadTarget(hash);
        });
        this.loadTarget();
    };
    /**
     * 切换页面并执行页面事件
     * @param hash 页面标识
     */
    Poncon.prototype.loadTarget = function (hash) {
        var target = this.getTarget(hash);
        var dom = this.getDom(target);
        var args = this.getArgs(hash);
        this.changeView(target);
        this.pages[target].event(target, dom, args);
    };
    /**
     * 获取页面参数列表
     * @param hash 网址Hash
     * @returns 页面参数列表
     */
    Poncon.prototype.getArgs = function (hash) {
        var strs = (hash || location.hash).split('/');
        if (strs.length < 3) {
            return [];
        }
        return strs.slice(2);
    };
    /**
     * 获取当前页面标识, 支持自动矫正
     * @param hash 网址hash
     * @returns 页面标识
     */
    Poncon.prototype.getTarget = function (hash) {
        var strs = (hash || location.hash).split('/');
        var target = strs[1] || '';
        // target不合法或者不在白名单
        if (target.search(/^\w+$/) != 0 || this.pageNames.indexOf(target) == -1) {
            history.replaceState({}, '', "".concat(location.pathname));
            return 'home';
        }
        return target;
    };
    /**
     * 获取页面DOM
     * @param target 页面标识
     * @returns 页面DOM元素
     */
    Poncon.prototype.getDom = function (target) {
        return document.querySelector(".poncon-".concat(target));
    };
    return Poncon;
}());
exports["default"] = Poncon;

},{}]},{},[2]);
