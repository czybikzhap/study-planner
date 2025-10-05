<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система выбора направлений</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Все предыдущие стили остаются */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: #4f46e5;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .directions-container {
            padding: 20px;
        }

        .direction {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .direction:hover {
            border-color: #4f46e5;
        }

        .direction-header {
            padding: 15px 20px;
            background: #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .direction-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
        }

        .direction-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-secondary {
            background: #64748b;
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .profiles-container {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .profiles-container.show {
            max-height: 1000px;
            padding: 20px;
        }

        .profile {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .profile:hover {
            border-color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .profile-info h3 {
            color: #1e293b;
            margin-bottom: 5px;
        }

        .profile-info p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .profile-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .priority-badge {
            background: #4f46e5;
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            min-width: 30px;
            text-align: center;
        }

        .save-container {
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .btn-save {
            background: #10b981;
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
        }

        .btn-save:hover {
            background: #059669;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .selection-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }

        .selection-info.warning {
            background: #fef3c7;
            border-color: #f59e0b;
        }

        .direction-checkbox {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .selected-count {
            font-weight: bold;
            color: #4f46e5;
        }

        .max-selection {
            font-weight: bold;
            color: #dc2626;
        }

        .direction.disabled {
            opacity: 0.6;
            background: #f1f5f9;
        }

        .direction.disabled .direction-header {
            cursor: not-allowed;
        }

        .btn-add {
            background: #10b981;
            color: white;
            margin-left: 10px;
        }

        .btn-remove {
            background: #ef4444;
            color: white;
            margin-left: 10px;
        }

        .available-directions {
            margin-top: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Выбор направлений обучения</h1>
        <p>Выберите до <span class="max-selection">3</span> направлений и расставьте приоритеты</p>
    </div>

    <div class="directions-container">
        <!-- Блок информации о выборе -->
        <div class="selection-info" id="selectionInfo">
            <div>Выбрано: <span class="selected-count" id="selectedCount">0</span> из <span class="max-selection">3</span> направлений</div>
            <div style="font-size: 0.9rem; margin-top: 5px; color: #64748b;">
                Нажмите на направление, чтобы открыть список профилей
            </div>
        </div>

        <!-- Выбранные направления (для приоритизации) -->
        <div id="selectedDirectionsContainer">
            <!-- Выбранные направления будут здесь -->
        </div>

        <!-- Доступные направления (для выбора) -->
        <div class="available-directions">
            <h3 style="margin-bottom: 15px; color: #374151;">Доступные направления</h3>
            <div id="availableDirectionsContainer">
                <!-- Невыбранные направления будут здесь -->
            </div>
        </div>
    </div>

    <div class="save-container">
        <button class="btn btn-save" onclick="savePriorities()">Сохранить выбор</button>
    </div>
</div>

<script>

    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    // Конфигурация
    const MAX_SELECTIONS = 3;

    // Способ 2: Используем toJson() из Laravel
    let directionsData = {!! $directions->toJson() !!};
    directionsData = directionsData.map(direction => ({
        ...direction,
        isOpen: false,
        isSelected: false,
        profiles: direction.profiles || []
    }));

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        renderAllDirections();
        updateSelectionInfo();
    });

    // Общий рендер всех направлений
    function renderAllDirections() {
        renderSelectedDirections();
        renderAvailableDirections();
    }

    // Рендер выбранных направлений (для приоритизации)
    function renderSelectedDirections() {
        const container = document.getElementById('selectedDirectionsContainer');
        const selectedDirections = directionsData.filter(d => d.isSelected);

        if (selectedDirections.length === 0) {
            container.innerHTML = `
                    <div class="empty-state">
                        <h3>Нет выбранных направлений</h3>
                        <p>Выберите направления из списка ниже</p>
                    </div>
                `;
            return;
        }

        container.innerHTML = selectedDirections.map((direction, index) => `
                <div class="direction" data-direction-id="${direction.id}">
                    <div class="direction-header" onclick="toggleSelectedDirection(${direction.id})">
                        <div class="direction-title">
                            <span class="priority-badge">${index + 1}</span>
                            ${direction.name} (${direction.number})
                        </div>
                        <div class="direction-actions">
                            <button class="btn btn-secondary btn-small" onclick="event.stopPropagation(); moveDirectionUp(${direction.id})">↑</button>
                            <button class="btn btn-secondary btn-small" onclick="event.stopPropagation(); moveDirectionDown(${direction.id})">↓</button>
                            <button class="btn btn-remove btn-small" onclick="event.stopPropagation(); deselectDirection(${direction.id})">✕</button>
                        </div>
                    </div>
                    <div class="profiles-container ${direction.isOpen ? 'show' : ''}" id="selected-profiles-${direction.id}">
                        ${renderProfiles(direction.profiles)}
                    </div>
                </div>
            `).join('');
    }

    // Рендер доступных направлений (для выбора)
    function renderAvailableDirections() {
        const container = document.getElementById('availableDirectionsContainer');
        const availableDirections = directionsData.filter(d => !d.isSelected);

        if (availableDirections.length === 0) {
            container.innerHTML = `
                    <div class="empty-state">
                        <h3>Все направления выбраны</h3>
                        <p>Вы можете изменить выбор, удалив некоторые направления</p>
                    </div>
                `;
            return;
        }

        container.innerHTML = availableDirections.map(direction => `
                <div class="direction ${getSelectedCount() >= MAX_SELECTIONS ? 'disabled' : ''}" data-direction-id="${direction.id}">
                    <div class="direction-header" onclick="toggleAvailableDirection(${direction.id})">
                        <div class="direction-title">
                            <input
                                type="checkbox"
                                class="direction-checkbox"
                                ${getSelectedCount() >= MAX_SELECTIONS ? 'disabled' : ''}
                                onchange="toggleDirectionSelection(${direction.id}, this.checked)"
                                ${direction.isSelected ? 'checked' : ''}
                            >
                            ${direction.name} (${direction.number})
                        </div>
                        <div class="direction-actions">
                            <button class="btn btn-add btn-small"
                                ${getSelectedCount() >= MAX_SELECTIONS ? 'disabled' : ''}
                                onclick="event.stopPropagation(); selectDirection(${direction.id})">
                                Добавить
                            </button>
                        </div>
                    </div>
                    <div class="profiles-container ${direction.isOpen ? 'show' : ''}" id="available-profiles-${direction.id}">
                        ${renderProfiles(direction.profiles)}
                    </div>
                </div>
            `).join('');
    }

    // Рендер профилей
    function renderProfiles(profiles) {
        if (!profiles || profiles.length === 0) {
            return '<div class="empty-state">Нет доступных профилей</div>';
        }

        return profiles.map((profile, index) => `
                <div class="profile" data-profile-id="${profile.id}">
                    <div class="profile-info">
                        <h3>${profile.name}</h3>
                        ${profile.description ? `<p>${profile.description}</p>` : ''}
                    </div>
                    <div class="profile-actions">
                        <span class="priority-badge">${index + 1}</span>
                        <button class="btn btn-secondary btn-small" onclick="moveProfileUp(${profile.id})">↑</button>
                        <button class="btn btn-secondary btn-small" onclick="moveProfileDown(${profile.id})">↓</button>
                    </div>
                </div>
            `).join('');
    }

    // Функции для работы с выбором направлений
    function getSelectedCount() {
        return directionsData.filter(d => d.isSelected).length;
    }

    function updateSelectionInfo() {
        const selectedCount = getSelectedCount();
        const countElement = document.getElementById('selectedCount');
        const infoElement = document.getElementById('selectionInfo');

        countElement.textContent = selectedCount;

        if (selectedCount >= MAX_SELECTIONS) {
            infoElement.classList.add('warning');
            infoElement.innerHTML = `
                    <div>Достигнут лимит: <span class="selected-count">${selectedCount}</span> из <span class="max-selection">${MAX_SELECTIONS}</span> направлений</div>
                    <div style="font-size: 0.9rem; margin-top: 5px; color: #dc2626;">
                        Для выбора нового направления удалите одно из выбранных
                    </div>
                `;
        } else {
            infoElement.classList.remove('warning');
            infoElement.innerHTML = `
                    <div>Выбрано: <span class="selected-count">${selectedCount}</span> из <span class="max-selection">${MAX_SELECTIONS}</span> направлений</div>
                    <div style="font-size: 0.9rem; margin-top: 5px; color: #64748b;">
                        Нажмите на направление, чтобы открыть список профилей
                    </div>
                `;
        }
    }

    function selectDirection(directionId) {
        if (getSelectedCount() >= MAX_SELECTIONS) {
            alert(`Можно выбрать не более ${MAX_SELECTIONS} направлений`);
            return;
        }

        const direction = directionsData.find(d => d.id === directionId);
        if (direction) {
            direction.isSelected = true;
            direction.isOpen = false; // Закрываем при добавлении
            renderAllDirections();
            updateSelectionInfo();
        }
    }

    function deselectDirection(directionId) {
        const direction = directionsData.find(d => d.id === directionId);
        if (direction) {
            direction.isSelected = false;
            direction.isOpen = false; // Закрываем при удалении
            renderAllDirections();
            updateSelectionInfo();
        }
    }

    function toggleDirectionSelection(directionId, isSelected) {
        if (isSelected && getSelectedCount() >= MAX_SELECTIONS) {
            alert(`Можно выбрать не более ${MAX_SELECTIONS} направлений`);
            return false;
        }

        const direction = directionsData.find(d => d.id === directionId);
        if (direction) {
            direction.isSelected = isSelected;
            direction.isOpen = false; // Закрываем при изменении выбора
            renderAllDirections();
            updateSelectionInfo();
        }
    }

    // Открытие/закрытие профилей для доступных направлений
    function toggleAvailableDirection(directionId) {
        const direction = directionsData.find(d => d.id === directionId);
        if (direction && !direction.isSelected) {
            direction.isOpen = !direction.isOpen;
            renderAvailableDirections();
        }
    }

    // Открытие/закрытие профилей для выбранных направлений
    function toggleSelectedDirection(directionId) {
        const direction = directionsData.find(d => d.id === directionId);
        if (direction && direction.isSelected) {
            direction.isOpen = !direction.isOpen;
            renderSelectedDirections();
        }
    }

    // Функции перемещения направлений
    function moveDirectionUp(directionId) {
        const selectedDirections = directionsData.filter(d => d.isSelected);
        const index = selectedDirections.findIndex(d => d.id === directionId);

        if (index > 0) {
            const actualIndex = directionsData.findIndex(d => d.id === directionId);
            const prevIndex = directionsData.findIndex(d => d.id === selectedDirections[index - 1].id);

            [directionsData[actualIndex], directionsData[prevIndex]] = [directionsData[prevIndex], directionsData[actualIndex]];
            renderSelectedDirections();
        }
    }

    function moveDirectionDown(directionId) {
        const selectedDirections = directionsData.filter(d => d.isSelected);
        const index = selectedDirections.findIndex(d => d.id === directionId);

        if (index < selectedDirections.length - 1) {
            const actualIndex = directionsData.findIndex(d => d.id === directionId);
            const nextIndex = directionsData.findIndex(d => d.id === selectedDirections[index + 1].id);

            [directionsData[actualIndex], directionsData[nextIndex]] = [directionsData[nextIndex], directionsData[actualIndex]];
            renderSelectedDirections();
        }
    }

    // Перемещение профилей
    function moveProfileUp(profileId) {
        for (let direction of directionsData) {
            const index = direction.profiles.findIndex(p => p.id === profileId);
            if (index > 0) {
                [direction.profiles[index], direction.profiles[index - 1]] = [direction.profiles[index - 1], direction.profiles[index]];
                renderSelectedDirections();
                break;
            }
        }
    }

    function moveProfileDown(profileId) {
        for (let direction of directionsData) {
            const index = direction.profiles.findIndex(p => p.id === profileId);
            if (index >= 0 && index < direction.profiles.length - 1) {
                [direction.profiles[index], direction.profiles[index + 1]] = [direction.profiles[index + 1], direction.profiles[index]];
                renderSelectedDirections();
                break;
            }
        }
    }

    // Сохранение приоритетов (только выбранных направлений)
    function savePriorities() {
        const selectedDirections = directionsData.filter(d => d.isSelected);

        if (selectedDirections.length === 0) {
            alert('Пожалуйста, выберите хотя бы одно направление');
            return;
        }

        const priorities = {
            directions: selectedDirections.map((direction, index) => ({
                id: direction.id,
                priority: index + 1,
                profiles: direction.profiles.map((profile, profileIndex) => ({
                    id: profile.id,
                    priority: profileIndex + 1
                }))
            }))
        };

        console.log('Отправляемые данные:', priorities);

        // Показываем индикатор загрузки
        const saveButton = document.querySelector('.btn-save');
        const originalText = saveButton.textContent;
        saveButton.textContent = 'Сохранение...';
        saveButton.disabled = true;

        fetch('http://localhost:8085/api/priorities/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',

            },
            body: JSON.stringify(priorities)
        })
            .then(response => {
                console.log('Статус ответа:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Ответ сервера:', data);
                if (data.success) {
                    alert('✅ ' + data.message);
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('❌ Ошибка при сохранении: ' + error.message);
            })
            .finally(() => {
                // Восстанавливаем кнопку
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            });
    }
</script>
</body>
</html>
