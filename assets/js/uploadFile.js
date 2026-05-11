const form = document.getElementById('uploadForm');
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
const uploadBtn = document.getElementById('uploadBtn');
const progress = document.getElementById('progress');
const bar = progress.querySelector('span');
const status = document.getElementById('status');

dropzone.addEventListener('dragover',(e)=>{e.preventDefault();dropzone.classList.add('dragover')});
dropzone.addEventListener('dragleave',(e)=>{e.preventDefault();dropzone.classList.remove('dragover')});

let droppedFile = null;

dropzone.addEventListener("drop", (e) => {
  e.preventDefault();
  dropzone.classList.remove("dragover");
  droppedFile = e.dataTransfer.files[0];
});

form.addEventListener('submit',(e)=>{
    e.preventDefault();

    if (!fileInput.files.length && !droppedFile) {
        status.textContent = "Veuillez sélectionner un fichier.";
        return;
    }

    console.log(form.action);

    const fd = new FormData();
    var file = droppedFile ?? fileInput.files[0];
    fd.append("file", file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action);

    progress.style.display='block';
    bar.style.width='0%';
    status.textContent='Téléversement en cours...';
    uploadBtn.disabled=true;

    xhr.upload.onprogress = (ev) => {
        if(ev.lengthComputable){
            const p = Math.round((ev.loaded / ev.total) * 100);
            bar.style.width = p + '%';
        }
    };

    xhr.onload = () => {
        uploadBtn.disabled = false;

        try {
            const response = JSON.parse(xhr.responseText);

            if (response.success) {
                status.textContent = "Téléversement réussi !";
                document.location.reload();
            } else {
                status.textContent = "Erreur : " + response.error;
            }
        } catch (e) {
            status.textContent = "Réponse inattendue du serveur.";
            console.log("Réponse inattendue : " + xhr.responseText);
        }
    };

    xhr.onerror = ()=> {
        uploadBtn.disabled=false;
        status.textContent='Erreur réseau.';
    };

    xhr.send(fd);
});