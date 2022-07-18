$(document).ready(function() {
    $("#page_form_images").on('change', function() {
        //Get count of selected files
        var countFiles = $(this)[0].files.length;
        var imgPath = $(this)[0].value;
        var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
        var image_holder = $("#image-holder");
        image_holder.empty();
        if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
            if (typeof(FileReader) != "undefined") {
                //loop for each file selected for uploaded.
                for (var i = 0; i < countFiles; i++)
                {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $("<img />", {
                            "src": e.target.result,
                            "class": "thumb-image"
                        }).appendTo(image_holder);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[i]);
                }
            } else {
                alert("This browser does not support FileReader.");
            }
        } else {
            alert("Pls select only images");
        }
    });
});

document.getElementById('page_form_images').addEventListener('click', () => {
document.querySelector(['[for=page_form_images]']).innerHTML = '⟲'
});

//modal 
const openBtn = document.querySelector(".post-md-help");
const closesBtn = document.querySelector(".closes-modal");
const overlay = document.querySelector(".overlay")
/////////////////////////////
openBtn.addEventListener("click", () => {
overlay.style.display = "block";
});

closesBtn.addEventListener("click", () => {
overlay.style.display = "none";
})


//navigation modal template moderne acces
const openBtn2 = document.querySelector(".open-modal2");
const closesBtn2 = document.querySelector(".closes-modal2");
const overlay2 = document.querySelector(".overlay2")
/////////////////////////////
openBtn2.addEventListener("click", () => {
overlay2.style.display = "block";
overlay.style.display = "none";
});

closesBtn2.addEventListener("click", () => {
overlay2.style.display = "none";
})

//navigation modal template moderne change
const openBtn3 = document.querySelector(".open-modal");
const openBtn5 = document.querySelector(".open-modal4");

/////////////////////////////
openBtn3.addEventListener("click", () => {
overlay.style.display = "block";
overlay2.style.display = "none";
});

openBtn5.addEventListener("click", () => {
overlay4.style.display = "block";
overlay2.style.display = "none";
});

// Navigation modal template experimental acces
const openBtn4 = document.querySelector(".open-modal3");
const closesBtn4 = document.querySelector(".closes-modal3");
const overlay4 = document.querySelector(".overlay3")
/////////////////////////////
openBtn4.addEventListener("click", () => {
overlay4.style.display = "block";
overlay.style.display = "none";
});

closesBtn4.addEventListener("click", () => {
overlay4.style.display = "none";
})

//navigation modal template moderne change
const openBtn6 = document.querySelector(".open-modal5");
const openBtn7 = document.querySelector(".open-modal6");

/////////////////////////////
openBtn6.addEventListener("click", () => {
overlay.style.display = "block";
overlay4.style.display = "none";
});

openBtn7.addEventListener("click", () => {
overlay2.style.display = "block";
overlay4.style.display = "none";
});

//Aparation de texte


$("#box-hidden").delay(6100).animate({"opacity": "1"}, 800);

$("#box-hidden2").delay(7100).animate({"opacity": "1"}, 800);


$("#box-hidden3").delay(8100).animate({"opacity": "1"}, 800);
