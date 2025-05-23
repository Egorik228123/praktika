function searchUsers(searchTerm) {
            const term = searchTerm.trim();
            const isNumericSearch = /^\d+$/.test(term); // Проверяем, состоит ли строка только из цифр

            const filtered = allUsers.filter(user => {
                // Точное совпадение для ID если поиск числовой
                if(isNumericSearch) {
                    return user.id.toString() === term;
                }
                // Частичный поиск для текстовых полей
                const lowerTerm = term.toLowerCase();
                return (
                    user.name.toLowerCase().includes(lowerTerm) ||
                    user.surname.toLowerCase().includes(lowerTerm) ||
                    (user.middlename && user.middlename.toLowerCase().includes(lowerTerm))
                );
            });
            
            renderUsers(filtered);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            
            searchInput.addEventListener('input', function(e) {
                searchUsers(e.target.value);
            });
        });