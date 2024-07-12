<script>
document.addEventListener("DOMContentLoaded",() => {
    const klickelemente = document.querySelectorAll(".artikel"); //sämtliche anklickbaren Elemente mit klasse artikel
    for(let i=0; i<klickelemente.length; i++) {
        klickelemente[i].addEventListener("click",(ev) => {
            
            // console.log(ev);
            // console.log(ev.srcElement.attributes["data-artikel"].nodeValue);
            // console.log("Ich wurde geklickt");
            
            document.querySelector("[name=inpArtikel").value = ev.srcElement.attributes["data-artikel"].nodeValue;
            document.querySelector("#id1").submit();
        });
    }
});
</script>