$(document).ready(function() {
    const themeToggle = $('#theme-toggle');
    const currentTheme = localStorage.getItem('theme') || 'light';
    $('body').addClass(currentTheme);
    themeToggle.html(currentTheme === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>');

    themeToggle.on('click', function() {
        $('body').toggleClass('dark');
        const theme = $('body').hasClass('dark') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        themeToggle.html(theme === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>');
    });

    $('#search-input').on('focus', function() {
        $('.navbar-search').addClass('expanded');
    });

    $('#search-input').on('input', function() {
        let query = $(this).val();
        if (query.length > 0) { // Changement de condition pour élargir immédiatement
            $.ajax({
                url: 'action/search.php',
                method: 'POST',
                data: {query: query},
                success: function(data) {
                    $('#search-results').html(data);
                }
            });
        } else {
            $('#search-results').html('');
        }
    });

    $('#search-input').on('blur', function() {
        if ($(this).val().length === 0) {
            $('.navbar-search').removeClass('expanded');
        }
    });

    $('#search-input').on('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            let query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: 'action/search_full.php',
                    method: 'POST',
                    data: {query: query},
                    success: function(data) {
                        $('#full-search-results').html(data);
                        $('#full-search-modal').show();
                    }
                });
            }
        }
    });

    $(window).on('click', function(event) {
        if (event.target.id === 'full-search-modal') {
            $('#full-search-modal').hide();
        }
    });

    $('.close').on('click', function() {
        $('#full-search-modal').hide();
    });

    const authButton = $('#auth-button');
    const modal = $('#auth-modal');
    const cancelButton = $('.cancelbtn');

    if (authButton.length) {
        authButton.on('click', function() {
            modal.show();
        });

        cancelButton.on('click', function() {
            modal.hide();
        });

        window.onclick = function(event) {
            if (event.target === modal[0]) {
                modal.hide();
            }
        }
    }

    // Ajoutez cette partie pour rendre les résultats cliquables
    $('#search-results').on('click', '.search-result', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        let url = '';

        switch (type) {
            case 'facture':
                url = '/facture.php?id=' + id;
                break;
            case 'client':
                url = '/client.php?id=' + id;
                break;
            case 'contact':
                url = '/contact.php?id=' + id;
                break;
        }

        if (url) {
            window.location.href = url;
        }
    });
});
