/* =====================================
   TEAM VIOLETS WEBSITE
===================================== */

/* Smooth Navigation */

document.querySelectorAll('a[href^="#"]').forEach(link => {

    link.addEventListener('click', function(e){

        const targetId = this.getAttribute('href');

        if(targetId === "#") return;

        const target =
        document.querySelector(targetId);

        if(target){

            e.preventDefault();

            target.scrollIntoView({
                behavior:'smooth',
                block:'start'
            });

        }

    });

});

/* =====================================
   SCROLL ANIMATION
===================================== */

const observer = new IntersectionObserver(

(entries)=>{

    entries.forEach(entry=>{

        if(entry.isIntersecting){

            entry.target.classList.add("show");

        }

    });

},

{
    threshold:0.15
}

);

document.querySelectorAll(
'.about-section, .stats-section, .team-category, .apply-section, .social-section'
).forEach(el=>{

    el.classList.add("hidden");

    observer.observe(el);

});

/* =====================================
   COUNTER ANIMATION
===================================== */

const counters =
document.querySelectorAll(".stat-box h3");

const speed = 80;

counters.forEach(counter=>{

    const animate = ()=>{

        const target =
        counter.innerText;

        const numeric =
        parseInt(target.replace(/\D/g,''));

        let count = 0;

        const update = ()=>{

            count += Math.ceil(numeric / speed);

            if(count >= numeric){

                counter.innerText = target;

                return;
            }

            if(target.includes("M")){

                counter.innerText =
                count + "M+";

            }else if(target.includes("+")){

                counter.innerText =
                count + "+";

            }else{

                counter.innerText =
                count;
            }

            requestAnimationFrame(update);

        };

        update();

    };

    animate();

});

/* =====================================
   HERO PARALLAX
===================================== */

window.addEventListener("scroll", ()=>{

    const heroLogo =
    document.querySelector(".hero-logo");

    if(heroLogo){

        const value =
        window.scrollY * 0.15;

        heroLogo.style.transform =
        `translateY(${value}px)`;
    }

});

/* =====================================
   BUTTON EFFECT
===================================== */

document.querySelectorAll(
'.primary-btn, .secondary-btn, .apply-btn'
).forEach(btn=>{

    btn.addEventListener('mouseenter',()=>{

        btn.style.transition = ".3s";

    });

});

/* =====================================
   ACTIVE NAV LINK
===================================== */

const sections =
document.querySelectorAll("section");

const navLinks =
document.querySelectorAll(".nav-links a");

window.addEventListener("scroll", ()=>{

    let current = "";

    sections.forEach(section=>{

        const top =
        section.offsetTop - 150;

        const height =
        section.clientHeight;

        if(window.scrollY >= top){

            current =
            section.getAttribute("id");
        }

    });

    navLinks.forEach(link=>{

        link.classList.remove("active");

        if(
            link.getAttribute("href")
            === "#" + current
        ){

            link.classList.add("active");

        }

    });

});

/* =====================================
   SOCIAL CARD HOVER
===================================== */

document.querySelectorAll(
'.social-card'
).forEach(card=>{

    card.addEventListener(
    "mousemove",

    (e)=>{

        const rect =
        card.getBoundingClientRect();

        const x =
        e.clientX - rect.left;

        const y =
        e.clientY - rect.top;

        card.style.setProperty(
            "--x",
            x + "px"
        );

        card.style.setProperty(
            "--y",
            y + "px"
        );

    });

});

/* =====================================
   TEAM VIOLETS
===================================== */

console.log(
"%cTEAM VIOLETS",
"color:#a855f7;font-size:32px;font-weight:bold;"
);

console.log(
"Rocket League Freestyle Team"
);

const applicationApiUrl = "https://script.google.com/macros/s/AKfycbwprZ6GZRA9_myaTsdh3kCqCVFiOiaEdRgiIxKStq1gXEqlBzmKJYF97zzZJUibBhFrDg/exec";

function getPlayerFormElements(){

    return {
        playerTypeField: document.getElementById("player-type-field"),
        freestyleTypeField: document.getElementById("freestyle-type-field"),
        freestyleOptions: document.getElementById("freestyle-options"),
        rlTracker: document.getElementById("rl-tracker-field")
    };

}

function syncPlayerMode(type){

    const elements = getPlayerFormElements();

    if(!elements.playerTypeField || !elements.freestyleTypeField || !elements.freestyleOptions || !elements.rlTracker){

        return;

    }

    elements.playerTypeField.value = type;

    if(type === "freestyle"){

        elements.freestyleOptions.style.display = "block";
        elements.rlTracker.style.display = "none";
        elements.rlTracker.required = false;

        const activeFreestyleButton = document.querySelector(".freestyle-btn.active");
        const selectedFreestyleButton = activeFreestyleButton || document.querySelector(".freestyle-btn");

        if(selectedFreestyleButton){

            if(!activeFreestyleButton){

                selectedFreestyleButton.classList.add("active");

            }

            elements.freestyleTypeField.value = selectedFreestyleButton.dataset.freestyleType || "";



        }

        return;

    }

    elements.freestyleOptions.style.display = "none";
    elements.rlTracker.style.display = "block";
    elements.rlTracker.required = true;
    elements.freestyleTypeField.value = "";

}

async function submitApplicationForm(event){

    event.preventDefault();

    const form = event.currentTarget;
    const submitButton = form.querySelector('button[type="submit"]');

    if(submitButton){
        submitButton.disabled = true;
    }

    try{

        const formData = new FormData(form);

        const response = await fetch(applicationApiUrl,{
            method:"POST",
            body:formData
        });

       const text = await response.text();

console.log(text);

const result = JSON.parse(text);

        if(result.success){

            alert("تم إرسال الطلب بنجاح ✅");

            form.reset();

            if(form.id==="player-form"){
                syncPlayerMode("freestyle");
            }

        }else{

            alert(result.error || "حدث خطأ");

        }

    }catch(error){

        console.error(error);

        alert("تعذر الاتصال بالخادم");

    }finally{

        if(submitButton){
            submitButton.disabled=false;
        }

    }

}


function showForm(event, type){

    document
    .querySelectorAll('.application-form')
    .forEach(form => {

        form.style.display = 'none';

    });

    document
    .querySelectorAll('.tab-btn')
    .forEach(btn => {

        btn.classList.remove('active');

    });

    const selectedForm =
    document.getElementById(type + '-form');

    if(selectedForm){

        selectedForm.style.display = 'flex';

    }

    event.currentTarget.classList.add('active');

}document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".application-form").forEach(form => {

    form.onsubmit = function(event){

        alert("JavaScript يعمل");

        event.preventDefault();

    };

});

    const playerForm =
    document.getElementById("player-form");

    const designerForm =
    document.getElementById("designer-form");

    const contentForm =
    document.getElementById("content-form");

    if(playerForm) playerForm.style.display = "flex";

    if(designerForm) designerForm.style.display = "none";

    if(contentForm) contentForm.style.display = "none";

    syncPlayerMode("freestyle");

});function showPlayerType(event, type){

    document
    .querySelectorAll('.player-type-btn')
    .forEach(btn => {

        btn.classList.remove('active');

    });

    event.currentTarget.classList.add('active');

    const playerTypeField =
    document.getElementById('player-type-field');

    if(playerTypeField){

        playerTypeField.value = type;

    }

    const freestyleOptions =
    document.getElementById(
        'freestyle-options'
    );

    const rlTracker =
    document.getElementById(
        'rl-tracker-field'
    );

    if(type === 'freestyle'){

        freestyleOptions.style.display =
        'block';

        rlTracker.style.display =
        'none';

        rlTracker.required = false;

        const freestyleTypeField =
        document.getElementById('freestyle-type-field');

        if(freestyleTypeField){

            const activeFreestyleButton =
            document.querySelector('.freestyle-btn.active');
            const selectedFreestyleButton =
            activeFreestyleButton || document.querySelector('.freestyle-btn');

            if(selectedFreestyleButton){

                freestyleTypeField.value =
                selectedFreestyleButton.dataset.freestyleType || '';

            }

        }

    }

    else{

        freestyleOptions.style.display =
        'none';

        rlTracker.style.display =
        'block';

        rlTracker.required = true;

        const freestyleTypeField =
        document.getElementById('freestyle-type-field');

        if(freestyleTypeField){

            freestyleTypeField.value = '';

        }

    }

}document.addEventListener("DOMContentLoaded", () => {

    const rlTracker =
    document.getElementById(
        "rl-tracker-field"
    );

    if(rlTracker){

        rlTracker.style.display = "none";
        rlTracker.required = false;

    }

});function selectFreestyle(event){

    document
    .querySelectorAll('.freestyle-btn')
    .forEach(btn => {

        btn.classList.remove('active');

    });

    event.currentTarget.classList.add('active');

    const freestyleTypeField =
    document.getElementById('freestyle-type-field');

    if(freestyleTypeField){

        freestyleTypeField.value =
        event.currentTarget.dataset.freestyleType || '';

    }

}