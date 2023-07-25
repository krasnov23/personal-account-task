
'use strict';

console.log('Some')



function serviceSubscribe()
{
    const subscribeToService = document.querySelector('.add-service');

    if (subscribeToService)
    {
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
            <button class="btn btn-outline-dark w-100 mt-3" type="submit">Создать</button>
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
    }

}


function moneyToBalance()
{
    const addMoneyToBalance = document.querySelector('.add-form-to-add-money');

    if (addMoneyToBalance)
    {
        addMoneyToBalance.addEventListener('click',(event) => {

            event.preventDefault();

            const parentOfButton = addMoneyToBalance.parentNode;

            if (!parentOfButton.classList.contains('add-money-div'))
            {
                parentOfButton.classList.add('add-money-div')

                let formAddMoney = document.createElement('div');

                formAddMoney.classList.add('add-money-form')

                formAddMoney.innerHTML +=`
                <form method="post">
                <label class="form-label mt-2">Введите сумму которую хотите положить</label>
                <input class="form-control form-control-lg" required type="number" min="1" max="1000000" step="1" name="amount-to-add" placeholder="" aria-label=".form-control-lg example">
                <button class="btn btn-outline-dark w-100 mt-3" type="submit">Пополнить</button>
                </form>        
`
                parentOfButton.appendChild(formAddMoney);
            }else {
                parentOfButton.classList.remove('add-money-div');
                // Удаляет последний элемент в родителе то есть форму
                parentOfButton.querySelector('.add-money-form').remove();

            }
        })
    }

}

function submitDeleteService()
{
    const allDeleteButtons = document.querySelectorAll('.delete-service');

    if (allDeleteButtons)
    {
        allDeleteButtons.forEach((deleteButton) => {
            deleteButton.addEventListener('click',(event) => {
                const result = confirm('Вы уверенны что хотите удалить данный сервис?');
                if (result === false)
                {
                    event.preventDefault();
                }
            })
        })
    }

}

function totalServicesCost()
{
    const allCells = document.querySelectorAll('.total-price-multiply-amount');
    let elementValue = 0;

    if (allCells)
    {
        allCells.forEach((cell) => {
            elementValue += Number(cell.textContent);
        })
    }

    const totalAmountMultiplyPriceByAllServices = document.querySelector('.total-for-all-services');
    if (totalAmountMultiplyPriceByAllServices)
    {
        totalAmountMultiplyPriceByAllServices.textContent = elementValue;
    }

}


totalServicesCost()
serviceSubscribe();
moneyToBalance();
submitDeleteService();
