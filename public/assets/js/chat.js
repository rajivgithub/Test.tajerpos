const form = document.querySelector(".typing-area"),
inputField = form.querySelector(".chat-input"),
sendBtn = form.querySelector("button"),
chatBox = document.querySelector(".chat-box-area");
CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}

sendBtn.onclick = ()=>{
    insert_chat_url = form.querySelector(".insert-chat-url").value;
    incoming_id = form.querySelector(".incoming-msg-id").value;
    message = inputField.value;
    $.ajax({
        url: insert_chat_url,
        type: 'post',
        data: {_token: CSRF_TOKEN,incoming_msg_id: incoming_id, message: message},
        success: function(response){
            inputField.value = "";
            scrollToBottom();
        }
    });
}
chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}

// setInterval(() =>{
//     get_chat_url = form.querySelector(".get-chat-url").value;
//     $.ajax({
//         url: get_chat_url,
//         type: 'get',
//         data: {_token: CSRF_TOKEN,incoming_msg_id: incoming_id},
//         success: function(response){
//             if(response.status === 200) {
//                 let data = response.chat_data;
//                 chatBox.innerHTML = data;
//                 if(!chatBox.classList.contains("active")) {
//                     scrollToBottom();
//                 }
//             }
//         }
//     });
// }, 5000); 

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
}
  