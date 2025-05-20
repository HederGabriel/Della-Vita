function toggleCheckbox(clicked) {
    const casa = document.getElementById('casa');
    const local = document.getElementById('local');
    const h2Casa = document.querySelector('.h2-casa');
    const h2Local = document.querySelector('.h2-local');

    if (clicked === casa && casa.checked) {
        local.checked = false;
        h2Casa.classList.add('h2-active');
        h2Local.classList.remove('h2-active');
    } else if (clicked === local && local.checked) {
        casa.checked = false;
        h2Local.classList.add('h2-active');
        h2Casa.classList.remove('h2-active');
    } else {
        h2Casa.classList.remove('h2-active');
        h2Local.classList.remove('h2-active');
    }
}
