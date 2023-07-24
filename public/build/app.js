
'use strict';

console.log('Some')

const subscribeToService = document.querySelector('.add-service');

subscribeToService.addEventListener('click', (event) =>{

    // Отключает поведение
    event.preventDefault();

    // Берет родителя родителя элемента
    const parentOfButton = subscribeToService.parentNode.parentNode;

    if (!parentOfButton.classList.contains('render-form'))
    {
        // Добавляет класс есои такого нет
        parentOfButton.classList.add('render-form');

        // Создает див элемент
        let formForNewService = document.createElement('div');

        // Добавляет класс к созданному элементу
        formForNewService.classList.add('div-service-form');

        // Добавляет внутренность этого элемента
        formForNewService.innerHTML += `
        <form method="post">
            <label class="form-label mt-2">Выберите услугу</label>
            <select class="form-select mb-3 mt-2" aria-label="Default select example" name="service-name" required>
            <option selected></option>
            <option value="вывоз мусора">вывоз мусора</option>
            <option value="электричество">электричество</option>
            <option value="лифт">лифт</option>
            </select>
            <label class="form-label">Введите количество желаемых услуг</label>
            <input class="form-control form-control-lg" required type="number" min="1" max="100" step="1" name="number-of-service" placeholder="Количество выбранных услуг" aria-label=".form-control-lg example">
            <button class="btn btn-outline-dark w-100 mt-3" type="submit">Button</button>
        </form>
        `
        //
        parentOfButton.appendChild(formForNewService);
    }else{
        // Удаляет класс родителя родителя элемента
        parentOfButton.classList.remove('render-form');

        // Удаляет последний элемент в родителе то есть форму
        parentOfButton.querySelector('.div-service-form').remove();
    }




})