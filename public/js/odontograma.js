let accion = document.getElementById('accion');
let dienteActivo = null;

document.querySelectorAll('.cara').forEach(cara=>{
    cara.addEventListener('click',()=>{
        if(!accion.value) return alert('Seleccione una acción');

        const diente = cara.closest('.diente');

        if(accion.value === 'extraccion'){
            diente.classList.add('extraido');
            guardar(diente.dataset.diente,'extraccion','todas');
            return;
        }

        cara.classList.toggle('activo');
        guardar(
            diente.dataset.diente,
            accion.value,
            cara.classList[1]
        );
    });
});

function guardar(diente,accion,cara){
    fetch(BASE_URL+'/odontograma/guardar',{
        method:'POST',
        body:new URLSearchParams({
            id_paciente:ID_PACIENTE,
            diente,
            accion,
            cara
        })
    });
}
