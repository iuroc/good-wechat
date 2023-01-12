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
                    }
                    ele_table.style.display = 'revert'
                    const eles = dom.querySelectorAll<HTMLTableCellElement>('td[contenteditable]')
                    eles.forEach(element => {
                        element.addEventListener('keydown', function (event: KeyboardEvent) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                event.preventDefault()
                            }
                        })
                        element.addEventListener('keyup', function (event) {
                            if (event.keyCode == 13 && !event.shiftKey) {
                                update(event)
                            }
                        })
                    })
                    const eles_delete = dom.querySelectorAll<HTMLDivElement>('td.delete')
                    eles_delete.forEach(element => {
                        element.addEventListener('click', function (event: Event) {
                            const ele_self = event.target as Node
                            const ele_parent = ele_self.parentNode
                            const id = (ele_self as HTMLElement).getAttribute('data-id')
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
                        })
                    })
                }
            })
            const ele_addKeyword = dom.querySelector('.addKeyword') as HTMLButtonElement
            ele_addKeyword.addEventListener('click', function () {
                const ele_firstLineDelete = dom.querySelector('.delete') as HTMLButtonElement
                if (ele_firstLineDelete && !ele_firstLineDelete.getAttribute('data-id')) {
                    const parentNode2 = ele_firstLineDelete.parentNode
                    parentNode2?.querySelector('td')?.focus()
                    return
                }
                const ele_tbody = dom.querySelector('tbody') as HTMLTableSectionElement
                const newEle = document.createElement('tr')
                newEle.innerHTML = `
                <td contenteditable="true" class="keyword"></td>
                <td contenteditable="true" class="reply"></td>
                <td class="text-nowrap text-danger user-select-none delete" role="button">删除</td>`
                ele_tbody.insertBefore(newEle, ele_tbody.children[0])
                const eles = dom.querySelectorAll<HTMLTableCellElement>('td[contenteditable]')
                eles.forEach(element => {
                    element.addEventListener('keydown', function (event: KeyboardEvent) {
                        if (event.keyCode == 13 && !event.shiftKey) {
                            event.preventDefault()
                        }
                    })
                    element.addEventListener('keyup', function (event) {
                        if (event.keyCode == 13 && !event.shiftKey) {
                            update(event)
                        }
                    })
                })
                newEle.querySelector('td')?.focus()
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

/**
 * 关键词回复列表，保存记录
 */
function update(event: KeyboardEvent) {
    const ele_self = event.target as HTMLElement
    const id = ele_self.getAttribute('data-id')
    const ele_keyword = ele_self.parentElement?.querySelector('td.keyword') as HTMLElement
    const ele_reply = ele_self.parentElement?.querySelector('td.reply') as HTMLElement
    const keyword = ele_keyword?.innerText
    const reply = ele_reply?.innerText
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
            } else {
                location.reload()
            }
        }
    })
}