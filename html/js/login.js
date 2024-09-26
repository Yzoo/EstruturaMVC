const salvar = document.getElementById('btnsalvar');
async function Insert() {
    const form = document.getElementById('form');
    const formData = new FormData(form);
    const opt = {
        method: 'POST',
        body: formData
    };
    const response = await fetch('/login/insert', opt);
    const json = await response.json();
    if (json.status != true) {
        alert('Verique os dados digitados e tente novamente!');
        return;
    }
    alert('Usuario cadastrado com sucesso!');
    return;
}
salvar.addEventListener('click', async () => {
    await Insert();
});