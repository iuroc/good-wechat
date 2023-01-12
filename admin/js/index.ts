import Ajax from './ajax'
import Poncon from './poncon'
const poncon = new Poncon()
poncon.setPageList(['home', 'about'])
const pageData = {
    home: {
        load: false,
        loadData(dom: HTMLElement) {
            new Ajax({
                url: 'get_keyword_list.php',
                success(data) {
                    pageData.home.load = true
                    const ele_tbody = dom.querySelector('tbody') as HTMLTableSectionElement
                    const ele_table = dom.querySelector('table') as HTMLTableElement
                    if (ele_table && ele_tbody && data) {
                        ele_tbody.innerHTML = data
                        ele_table.style.display = 'revert'
                    }
                    const eles = dom.querySelectorAll<HTMLTableCellElement>('td[contenteditable]')
                    eles.forEach(element => {
                        element.addEventListener('keydown', function (event: KeyboardEvent) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                event.preventDefault()
                            }
                        })
                        element.addEventListener('keyup', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                if (confirm('确定要修改这个记录？')) {
                                    const ele_self = event.target as HTMLDivElement
                                    const id = ele_self.getAttribute('data-id')
                                    const ele_keyword = document.querySelector('td.keyword[data-id="' + id + '"]') as HTMLTableCellElement
                                    const ele_reply = document.querySelector('td.reply[data-id="' + id + '"]') as HTMLTableCellElement
                                    const keyword = ele_keyword.innerText
                                    const reply = ele_reply.innerText
                                    new Ajax({
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
                    const eles_delete = dom.querySelectorAll<HTMLDivElement>('td.delete')
                    eles_delete.forEach(element => {
                        element.addEventListener('click', function (event: Event) {
                            const ele_self = event.target as Node
                            const ele_parent = ele_self.parentNode
                            const id = (ele_self as HTMLElement).getAttribute('data-id')
                            if (confirm('确定要删除该条记录？')) {
                                new Ajax({
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
                                        (ele_parent as HTMLElement).remove()
                                    }
                                })
                            }
                        })
                    })
                }
            })
            const ele_addKeyword = dom.querySelector('.addKeyword') as HTMLButtonElement
            ele_addKeyword.addEventListener('click', function () {
                const ele_firstLineDelete = dom.querySelector('.delete') as HTMLButtonElement
                if (!ele_firstLineDelete.getAttribute('data-id')) {
                    return
                }
                const ele_tbody = dom.querySelector('tbody') as HTMLTableSectionElement
                const newEle = document.createElement('tr')
                newEle.innerHTML = `
                <td contenteditable="true" class="keyword"></td>
                <td contenteditable="true" class="reply"></td>
                <td class="text-nowrap text-danger user-select-none delete" role="button">删除</td>`
                ele_tbody.insertBefore(newEle, ele_tbody.children[0]);
                (newEle.querySelector('td') as HTMLTableCellElement).focus()
            })
        }
    }
}
poncon.setPage('home', function (target, dom) {
    if (!pageData.home.load) {
        pageData.home.loadData(dom as HTMLElement)
    }
})
poncon.start()