const poncon = new Poncon()
poncon.setPageList(['home', 'about'])
const pageData = {
    home: {
        load: false,
        loadData(dom) {
            Ajax({
                url: 'get_keyword_list.php',
                success(data) {
                    this.load = true
                    dom.querySelector('tbody').innerHTML = data
                    dom.querySelector('table').style.display = 'revert'
                    const eles = dom.querySelectorAll('td[contenteditable]')
                    eles.forEach(element => {
                        element.addEventListener('keydown', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                event.preventDefault()
                            }
                        })
                        element.addEventListener('keyup', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                if (confirm('确定要修改这个记录？')) {
                                    const ele_self = event.target
                                    const id = ele_self.getAttribute('data-id')
                                    const ele_keyword = document.querySelector('td.keyword[data-id="' + id + '"]')
                                    const ele_reply = document.querySelector('td.reply[data-id="' + id + '"]')
                                    const keyword = ele_keyword.innerText
                                    const reply = ele_reply.innerText
                                    Ajax({
                                        url: 'update_keyword.php',
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        content: 'keyword=' + encodeURIComponent(keyword)
                                            + '&reply=' + encodeURIComponent(reply)
                                            + '&id=' + id,
                                        success(data) {
                                            if (!data) {
                                                alert('修改失败')
                                            }
                                        }
                                    })
                                }
                            }
                        })
                    })
                    const eles_delete = dom.querySelectorAll('td.delete')
                    eles_delete.forEach(element => {
                        element.addEventListener('click', function (event) {
                            const ele_self = event.target
                            const ele_parent = ele_self.parentNode
                            const id = ele_self.getAttribute('data-id')
                            if (confirm('确定要删除该条记录？')) {
                                Ajax({
                                    url: 'delete_keyword.php',
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    content: 'id=' + id,
                                    success(data) {
                                        if (!data) {
                                            alert('删除失败')
                                        }
                                        ele_parent.remove()
                                    }
                                })
                            }
                        })
                    })
                }
            })
            const ele_addKeyword = dom.querySelector('.addKeyword')
            ele_addKeyword.addEventListener('click', function () {
                const ele_firstLineDelete = dom.querySelector('.delete')
                if (!ele_firstLineDelete.getAttribute('data-id')) {
                    return
                }
                const ele_tbody = dom.querySelector('tbody')
                const newEle = document.createElement('tr')
                newEle.innerHTML = `
                <td contenteditable="true" class="keyword"></td>
                <td contenteditable="true" class="reply"></td>
                <td class="text-nowrap text-danger user-select-none delete" role="button">删除</td>`
                ele_tbody.insertBefore(newEle, ele_tbody.children[0])
                newEle.querySelector('td').focus()
            })
        }
    }
}
poncon.setPage('home', function (target, dom) {
    if (!pageData.home.load) {
        pageData.home.loadData(dom)
    }
})
poncon.start()