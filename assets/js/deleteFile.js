function delFile(lang, file) {
    const fd = new FormData();
    fd.append("file", file);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', "/"+lang+"/ajax/deleteFile");
    xhr.send(fd);
    document.location.reload();
}