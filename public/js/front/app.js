function goToByScroll(id) {
    $('html,body').animate({
        scrollTop: $("#" + id).offset().top
    }, 'slow');

}
window.addEventListener('scroll', (e) => {

    changeColorMenu(window.scrollY);

})

let root = document.documentElement;

function changeColorMenu(data) {

    let sections = document.querySelectorAll('.section, header');
    let menuItem = document.querySelectorAll('.nav-item');

    let scrollPos = data;
    const navbar = document.querySelector('.navbar');
    currentActiveMenuItem = ['#header'];


    if (scrollPos > sections[0].offsetTop && scrollPos < sections[0].offsetHeight) {
        changeColorItem(menuItem[0], sections[0].getAttribute('data-colormenuItem'))
        setAnimationOnMenuItem('data');
        changeColor(navbar, "header", sections[0].getAttribute('data-colormenu'))
       
        setFocus(0, menuItem)
    } else if (scrollPos > sections[1].offsetTop && scrollPos < (sections[1].offsetTop + sections[1].offsetHeight)) {
        changeColorItem(menuItem[1], sections[1].getAttribute('data-colormenuItem'))
        changeColor(navbar, "a-propos", sections[1].getAttribute('data-colormenu'))
        setFocus(1, menuItem)
    } else if (scrollPos > sections[2].offsetTop && scrollPos < (sections[2].offsetTop + sections[2].offsetHeight)) {

        changeColorItem(menuItem[2], sections[2].getAttribute('data-colormenuItem'))
        changeColor(navbar, "services", sections[2].getAttribute('data-colormenu'))
        setFocus(2, menuItem)
    } else if (scrollPos > sections[3].offsetTop - 500 ) {
        changeColorItem(menuItem[3], sections[3].getAttribute('data-colormenuItem'))
        changeColor(navbar, "contact", sections[3].getAttribute('data-colormenu'))
        setFocus(3, menuItem)
    }

}

function changeColor(el, nameSection, color) {
    el.style.background = color;
    el.setAttribute('data-sectionActive', nameSection)
    
}

function changeColorItem(el, color) {

    root.style.setProperty('--colorMenuItem', color);
}

function setAnimationOnMenuItem(el) {
    previous = document.querySelector(currentActiveMenuItem[0]).classList.add('active');
    // .classList.toggle('active');
    
}

function setFocus(n, menuList) {
    

    switch (n) {
        case 0:
        menuList[0].classList.add('active');
        menuList[1].classList.remove('active');
        menuList[2].classList.remove('active');
        menuList[3].classList.remove('active');

            break;
        case 1:
        menuList[0].classList.remove('active');
        menuList[1].classList.add('active');
        menuList[2].classList.remove('active');
        menuList[3].classList.remove('active');

            break;
        case 2:
        menuList[0].classList.remove('active');
        menuList[1].classList.remove('active');
        menuList[2].classList.add('active');
        menuList[3].classList.remove('active');

            break;
        case 3:
        menuList[0].classList.remove('active');
        menuList[1].classList.remove('active');
        menuList[2].classList.remove('active');
        menuList[3].classList.add('active');

            break;
    }
}