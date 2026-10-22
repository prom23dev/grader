// Обработка кастомного инпута для ввода населенного пункта

function initCustomSettlementInput() {
    const input = document.getElementById('custom-settlement-input');
    const button = document.getElementById('custom-settlement-button');
    const settlementsUl = document.querySelector('#settlements ul');
    
    if (!input || !button || !settlementsUl) {
        return;
    }

    button.addEventListener('click', function() {
        handleSearchOrAddSettlement();
    });

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSearchOrAddSettlement();
        }
    });

    input.addEventListener('input', function() {
        // Убираем активное состояние со всех элементов списка при вводе
        document.querySelectorAll('.settlement-item').forEach(s => {
            s.classList.remove('active');
            s.classList.remove('highlighted');
        });
        
        // Подсвечиваем совпадения при вводе
        highlightMatches(this.value.trim());
    });
}

function highlightMatches(searchValue) {
    if (!searchValue) {
        return;
    }
    
    const settlementsUl = document.querySelector('#settlements ul');
    const allItems = settlementsUl.querySelectorAll('.settlement-item');
    
    allItems.forEach(item => {
        const itemText = item.dataset.settlement.toLowerCase();
        const searchLower = searchValue.toLowerCase();
        
        if (itemText.includes(searchLower)) {
            item.classList.add('highlighted');
        }
    });
}

function selectSettlementItem(item) {
    document.querySelectorAll('.settlement-item').forEach(s => {
        s.classList.remove('active');
        s.classList.remove('highlighted');
    });
    item.classList.add('active');

    if (typeof selectedSettlement !== 'undefined') {
        selectedSettlement = item.dataset.settlement;
    }
    if (typeof selectedSettlementId !== 'undefined') {
        selectedSettlementId = item.dataset.settlementId;
    }

    if (typeof getProjects === 'function') {
        const regionId = typeof selectedRegionId !== 'undefined' ? selectedRegionId : '';
        const districtId = typeof selectedDistrictId !== 'undefined' ? selectedDistrictId : '';
        getProjects(regionId, districtId, item.dataset.settlementId);
    }

    if (typeof isMobile === 'function' && isMobile()) {
        setTimeout(() => {
            const projectsDiv = document.getElementById('projects');
            if (projectsDiv) {
                projectsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
}

function handleSearchOrAddSettlement() {
    const input = document.getElementById('custom-settlement-input');
    const settlementsUl = document.querySelector('#settlements ul');
    const settlementName = input.value.trim();

    if (!settlementName) {
        alert('Пожалуйста, введите название населенного пункта');
        return;
    }

    // Ищем существующий элемент с точным совпадением (без учета регистра)
    const existingItems = settlementsUl.querySelectorAll('.settlement-item');
    const existingItem = Array.from(existingItems).find(item =>
        item.dataset.settlement.toLowerCase() === settlementName.toLowerCase()
    );

    if (existingItem) {
        selectSettlementItem(existingItem);
        input.value = '';
        return;
    }

    // Населённого пункта нет в списке — отправляем на сервер как
    // неактивный (на модерации). В общий список он попадёт только
    // после того, как модератор его активирует.
    fetch('/map/add-settlement.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            district_id: typeof selectedDistrictId !== 'undefined' ? selectedDistrictId : '',
            name: settlementName
        })
    })
        .then(response => response.json())
        .then(result => {
            if (!result.success) {
                alert('Не удалось добавить населённый пункт. Попробуйте ещё раз.');
                console.error('Ошибка добавления населённого пункта:', result.error);
                return;
            }

            const newItem = document.createElement('li');
            newItem.className = 'settlement-item' + (result.active ? '' : ' settlement-item--pending');
            newItem.dataset.settlement = result.name;
            newItem.dataset.settlementId = result.id;
            newItem.textContent = result.active ? result.name : result.name + ' (на модерации)';

            newItem.addEventListener('click', function() {
                selectSettlementItem(this);
            });

            settlementsUl.appendChild(newItem);
            input.value = '';

            selectSettlementItem(newItem);
        })
        .catch(error => {
            alert('Не удалось добавить населённый пункт. Попробуйте ещё раз.');
            console.error('Ошибка добавления населённого пункта:', error);
        });
}

