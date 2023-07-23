
'use strict';

const subscribeToService = document.querySelector('.add-service');

subscribeToService.addEventListener('click', (event) =>{

    event.preventDefault();

    const parentOfButton = subscribeToService.parentNode.parentNode;

    if (!parentOfButton.classList.contains('render-form'))
    {
        parentOfButton.classList.add('render-form');

        let formForNewService = document.createElement('div');
        formForNewService.classList.add('div-service-form')

        formForNewService.innerHTML += `
        
        `
        parentOfButton.appendChild(formForNewService);
    }else{
        parentOfButton.classList.remove('render-form');

        parentOfButton.querySelector('.div-service-form').remove();
    }




})