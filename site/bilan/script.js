let nav = document.querySelector('nav');
let menu = document.querySelector('.menu');
let searchbar = document.querySelector('.searchbar');

menu.onclick = function(){
    nav.classList.toggle('active')
    searchbar.classList.toggle('active')
}