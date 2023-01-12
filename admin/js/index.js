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
                        ele_table.style.display = 'revert';
                    }
                    var eles = dom.querySelectorAll('td[contenteditable]');
                    eles.forEach(function (element) {
                        element.addEventListener('keydown', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                event.preventDefault();
                            }
                        });
                        element.addEventListener('keyup', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                if (confirm('确定要修改这个记录？')) {
                                    var ele_self = event.target;
                                    var id = ele_self.getAttribute('data-id');
                                    var ele_keyword = document.querySelector('td.keyword[data-id="' + id + '"]');
                                    var ele_reply = document.querySelector('td.reply[data-id="' + id + '"]');
                                    var keyword = ele_keyword.innerText;
                                    var reply = ele_reply.innerText;
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
                                        }
                                    });
                                }
                            }
                        });
                    });
                    var eles_delete = dom.querySelectorAll('td.delete');
                    eles_delete.forEach(function (element) {
                        element.addEventListener('click', function (event) {
                            var ele_self = event.target;
                            var ele_parent = ele_self.parentNode;
                            var id = ele_self.getAttribute('data-id');
                            if (confirm('确定要删除该条记录？')) {
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
                            }
                        });
                    });
                }
            });
            var ele_addKeyword = dom.querySelector('.addKeyword');
            ele_addKeyword.addEventListener('click', function () {
                var ele_firstLineDelete = dom.querySelector('.delete');
                if (!ele_firstLineDelete.getAttribute('data-id')) {
                    return;
                }
                var ele_tbody = dom.querySelector('tbody');
                var newEle = document.createElement('tr');
                newEle.innerHTML = "\n                <td contenteditable=\"true\" class=\"keyword\"></td>\n                <td contenteditable=\"true\" class=\"reply\"></td>\n                <td class=\"text-nowrap text-danger user-select-none delete\" role=\"button\">\u5220\u9664</td>";
                ele_tbody.insertBefore(newEle, ele_tbody.children[0]);
                newEle.querySelector('td').focus();
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
