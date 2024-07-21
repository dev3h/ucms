import { MASTER_MENUS} from '@/Store/Const/menu'

export function searchMenu() {
    let pathActive = window.location.pathname?.split('/')?.slice(0, 4).join('/')
    let pathSubmenu = window.location.pathname.split('/')
    let menus = MASTER_MENUS

    for (let item of menus) {
        if (item?.subMenus) {
            let findSubMenu = item.subMenus.find(function (submenu) {
                return pathActive.includes(submenu.pathActive)
            })
            if (findSubMenu) {
                return findSubMenu
            }
        } else {
            let findMenu = pathSubmenu?.findIndex(i => i === item.pathActive)
            if (findMenu > -1) {
                return item
            }
        }
    }
    return ''
}
