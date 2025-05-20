document.querySelectorAll('.user-btn').forEach(button => {
    button.addEventListener('click', async function() {
        const action = this.dataset.action;
        const formData = new FormData(document.getElementById('userForm'));
        formData.append('action', action);
        
        // Для действий с ID
        if(['delete', 'view', 'edit'].includes(action)) {
            formData.append('id', this.dataset.id || document.getElementById('userId').value);
        }

        try {
            const response = await fetch('/praktika-models/api/users/handler.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            // Визуализация ответа
            const responseDiv = document.getElementById('response');
            responseDiv.innerHTML = data.success 
                ? `<div class="success">${data.message}</div>` 
                : `<div class="error">${data.error}</div>`;
            
            // Обработка специфических действий
            if(action === 'delete' && data.success) {
                this.closest('button').remove();
            }
            if(action === 'view' && data.success) {
                // Заполнение формы данными пользователя
                Object.entries(data.data).forEach(([key, value]) => {
                    const field = document.querySelector(`[name="${key}"]`);
                    if(field) field.value = value;
                });
            }
            
        }
        catch(error) {
            console.error('Error:', error);
            document.getElementById('response').innerHTML = 
                `<div class="error">Network error: ${error.message}</div>`;
        }
    });
});

// Вывод всех пользователей

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/praktika-models/api/users/handler.php', {
            method: 'POST',
            body: new URLSearchParams({action: 'get-all'})
        });
        
        const data = await response.json();
        if(data.success) {
            const usersList = data.data.map(user => 
                `<div>${user.name} (${user.email})</div>`
            ).join('');
            document.getElementById('response').innerHTML = 
                `<h3>Все пользователи:</h3>${usersList}`;
        }
    }
    catch(error) {
        console.error('Ошибка загрузки:', error);
    }
});