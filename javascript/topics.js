topicsList = document.querySelector(".topics-list");

getTopics();



  function getTopics() {
    let xhr = new XMLHttpRequest();
    let url = "php/topics.php?action=get-contact-list";
    xhr.open("GET", url, true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          let data = xhr.response;
          console.log(data);
            topicsList.innerHTML = data;
             // Llamar a settingTopic después de cargar los temas
             //settingTopic();

          
        }
      }
    };
    xhr.send();

  }

  //settingTopic();
  
function settingTopic() {
  document.addEventListener('DOMContentLoaded', (event) => {
    const container = document.querySelector('.topics-list');
    let isDown = false;
    let startX;
    let scrollLeft;

    container.addEventListener('mousedown', (e) => {
      isDown = true;
      container.classList.add('active');
      startX = e.pageX - container.offsetLeft;
      scrollLeft = container.scrollLeft;
    });

    container.addEventListener('mouseleave', () => {
      isDown = false;
      container.classList.remove('active');
    });

    container.addEventListener('mouseup', () => {
      isDown = false;
      container.classList.remove('active');
    });

    container.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - container.offsetLeft;
      const walk = (x - startX) * 2; // velocidad de desplazamiento
      container.scrollLeft = scrollLeft - walk;
    });
  });
}
  