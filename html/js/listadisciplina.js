const table = new DataTable('#tabela', {
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: true,
    stateSave: true,
    select: true,
    language: {
        url: '/js/pt-BR.json'
    },
});
async function Delete(id) {
    document.getElementById('id').value = id;
    const form = document.getElementById('form');
    const formData = new FormData(form);
    const opt = {
        method: 'POST',
        body: formData
    }
    const response = await fetch('/disciplina/delete', opt);
    const json = await response.json();
    $('#tr' + id).remove();
}