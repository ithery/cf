const initMenuProperties = () => {
    document.querySelectorAll('.cres-menu').forEach((menu) => {
        menu.style.display = 'block';
    });
    document.querySelectorAll('[cres-menu-effect="menu-reveal"]').forEach((menu) => {
            menu.style.display = 'none';
    });
    var menuLeftRight = document.querySelectorAll(
        '.cres-menu-box-left, .cres-menu-box-right'
    );
    menuLeftRight.forEach(function (menu) {
        if (menu.getAttribute('cres-menu-width') === 'cover') {
            menu.style.width = '100%';
        } else {
            menu.style.width = menu.getAttribute('cres-menu-width') + 'px';
        }
    });
    var menuBottomTopModal = document.querySelectorAll(
        '.cres-menu-box-bottom, .cres-menu-box-top, .cres-menu-box-modal'
    );
    menuBottomTopModal.forEach(function (menu) {
        if (menu.getAttribute('cres-menu-width') === 'cover') {
            menu.style.width = '100%';
            menu.style.height = '100%';
        } else {
            menu.style.width = menu.getAttribute('cres-menu-width') + 'px';
            menu.style.height = menu.getAttribute('cres-menu-height') + 'px';
        }
    });
};
const applyMenuParallaxEffect = (menuElement, affectedSelector) => {
    var menuWidth = menuElement.getAttribute('cres-menu-width');
    var menuOffsetHeight = menuElement.offsetHeight;
    const affectedContainers = document.querySelectorAll(affectedSelector);
    if (menuElement.classList.contains('cres-menu-box-left')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateX(' + menuWidth / 10 + 'px)';
        }
    }
    if (menuElement.classList.contains('cres-menu-box-right')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateX(-' + menuWidth / 10 + 'px)';
        }
    }
    if (menuElement.classList.contains('cres-menu-box-bottom')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateY(-' + menuOffsetHeight / 5 + 'px)';
        }
    }
    if (menuElement.classList.contains('cres-menu-box-top')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateY(' + menuOffsetHeight / 5 + 'px)';
        }
    }
};
const applyMenuPushEffect = (menuElement, affectedSelector) => {
    var menuWidth = menuElement.getAttribute('cres-menu-width');
    var menuOffsetHeight = menuElement.offsetHeight;
    const affectedContainers = document.querySelectorAll(affectedSelector);
    if (menuElement.classList.contains('cres-menu-box-left')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateX(' + menuWidth + 'px)';
        }
    }
    if (menuElement.classList.contains('cres-menu-box-right')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateX(-' + menuWidth + 'px)';
        }
    }
    if (menuElement.classList.contains('cres-menu-box-bottom')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateY(-' + menuOffsetHeight + 'px)';
        }
    }
    if (menuElement.classList.contains('cres-menu-box-top')) {
        for (let i = 0; i < affectedContainers.length; i++) {
            affectedContainers[i].style.transform =
                'translateY(' + menuOffsetHeight + 'px)';
        }
    }
};
const initMenuTrigger = () => {
    var menuTriggers = document.querySelectorAll('[cres-menu]');

    menuTriggers.forEach((trigger) => {
        return trigger.addEventListener('click', (event) => {
            const allActiveMenu =
                document.querySelectorAll('.cres-menu-active');
            for (let i = 0; i < allActiveMenu.length; i++) {
                allActiveMenu[i].classList.remove('cres-menu-active');
            }
            var menuId = trigger.getAttribute('cres-menu');

            document.getElementById(menuId).classList.add('cres-menu-active');
            document
                .getElementsByClassName('cres-menu-hider')[0]
                .classList.add('cres-menu-active');
            const menuElement = document.getElementById(menuId);
            var menuEffect = menuElement.getAttribute('cres-menu-effect');

            const affectedSelector = menuElement.getAttribute('cres-menu-effect-selector') ?? 'body';
            var hiddenTimer = menuElement.getAttribute('cres-menu-hide');
            if (hiddenTimer) {
                setTimeout(function () {
                    document
                        .getElementById(menuId)
                        .classList.remove('cres-menu-active');
                    document
                        .getElementsByClassName('cres-menu-hider')[0]
                        .classList.remove('cres-menu-active');
                }, hiddenTimer);
            }
            if (menuEffect === 'menu-push') {

                applyMenuPushEffect(menuElement, affectedSelector);
            }
            if (menuEffect === 'menu-parallax') {
                applyMenuParallaxEffect(menuElement, affectedSelector);
            }
        });
    });
};
const initMenuHider = () => {
    if (!document.querySelectorAll('.cres-menu-hider').length) {
        let node = document.createElement("div");
        node.classList.add('cres-menu-hider');
        document.body.appendChild(node);
    }
    var menuHider = document.querySelectorAll('.cres-menu-hider');

    if (menuHider[0].classList.contains('cres-menu-active')) {
        menuHider[0].classList.add('disabled');
        menuHider[0].classList.remove('cres-menu-active');
        menuHider[0].style.transform = 'translateX(0px)';
        setTimeout(function () {
            menuHider[0].classList.remove('disabled');
        });
    }
    const menuHiderTriggers = document.querySelectorAll(
        '.cres-menu-close, .cres-menu-hider'
    );
    menuHiderTriggers.forEach((trigger) => {
        return trigger.addEventListener('click', (event) => {
            var menuReveals = document.querySelectorAll(
                '[cres-menu-effect="menu-reveal"]'
            );
            setTimeout(function () {
                for (let i = 0; i < menuReveals.length; i++) {
                    menuReveals[i].style.display = 'none';
                }
            }, 270);
            const menuActives = document.querySelectorAll('.cres-menu-active');
            menuHider[0].style.transform = 'translateX(0px)';
            for (let i = 0; i < menuActives.length; i++) {
                menuActives[i].classList.remove('cres-menu-active');
                const affectedSelector = menuActives[i].getAttribute('cres-menu-effect-selector') ?? 'body';
                const affectedContainers = document.querySelectorAll(affectedSelector);
                for (let j = 0; j < affectedContainers.length; j++) {
                    affectedContainers[j].style.transform = 'none';
                }

            }
        });
    });
};

export const initMenu = () => {
    initMenuProperties();
    initMenuTrigger();
    initMenuHider();
};
