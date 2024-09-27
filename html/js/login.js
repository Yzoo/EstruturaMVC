const salvar = document.getElementById('btnsalvar');
const Alerta = document.getElementById('alert');

async function Insert() {
    const form = document.getElementById('form');
    const formData = new FormData(form);
    const opt = {
        method: 'POST',
        body: formData
    };
    const response = await fetch('/cadastro/insert', opt);
    const json = await response.json();
    return json;
}

async function insert() {
    Alerta.className = 'alert alert-info';
    Alerta.innerHTML = 'Salvando, por favor aguarde...';
    const response = await Insert();
    if (response.status !== true) {
        Alerta.className = 'alert alert-danger';
        Alerta.innerHTML = response.msg;
        setTimeout(() => {
            Alerta.className = 'alert alert-warning';
            Alerta.innerHTML = 'Todos os campos com <span class="text text-danger">*</span> são obrigatórios!';
        }, 2000);
        return;
    }
    Alerta.className = 'alert alert-success';
    Alerta.innerHTML = response.msg;
}
salvar.addEventListener('click', async (event) => {
    event.preventDefault(); // Impede o envio padrão do formulário
    await Insert();

});