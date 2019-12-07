$(document).ready(() => {
  // 取得所有todolist
  $.ajax({
    type: 'GET',
    url: 'http://localhost:8080/K/todolist/list',
  }).done((res) => {
    // jQuery 會自動 parse，不用再轉
    $.each(res, (index, el) => {
      $('.list-group').append(getTodo(el.id, el.content, el.isCompleted));
    })
  });
  // post 新的 todo
  $('.list-header').on('click', '.btn', addTodo);
  $('.form-control').keypress((e) => {
    if (e.key === 'Enter') addTodo();
  })
  // delete todo
  $('.list-group').on('click', '.btn-outline-danger', function(){
    const id = $(this).val();
    $.ajax({
      type: 'DELETE',
      url: `http://localhost:8080/K/todolist/list/${id}`,
    }).done((res) =>{
      $(this).parent().parent().remove();
    })
  });
  // 更改內容
  $('.list-group').on('keyup', '.form-control', function() {
    const id = $(this).parent().find('button[type="button"]').val();
    const content = $(this).val();
    const isCompleted = $(this).parent().find('input[type="checkbox"]').prop('checked') ? 1 : 0;
    $.ajax({
      type: 'PATCH',
      url: `http://localhost:8080/K/todolist/list/${id}`,
      data: {
        content,
        isCompleted,
      }
    })
  })
  // 更改狀態
  $('.list-group').on('click', 'input[type="checkbox"]', function() {
    const id = $(this).parent().parent().parent().find('button[type="button"]').val();
    const content = $(this).parent().parent().parent().find('input[type="text"]').val();
    (this.checked) ? $(this).prop('checked', true) : $(this).prop('checked', false);
    const isCompleted = $(this).prop('checked') ? 1 : 0;
    $.ajax({
      type: 'PATCH',
      url: `http://localhost:8080/K/todolist/list/${id}`,
      data: {
        content,
        isCompleted,
      }
    })
    $(this).parent().parent().parent().find('input[type="text"]').toggleClass('isCompleted');
  })
});

function getTodo(id, content, isCompleted) {
  const checked = (isCompleted === 1) ? 'checked' : '';
  const done = (isCompleted === 1) ? 'isCompleted' : '';
  return `      
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <div class="input-group-text">
          <input type="checkbox" aria-label="Checkbox for following text input" ${checked} value="${isCompleted}">
        </div>
      </div>
      <input type="text" class="form-control ${done}" aria-label="Text input with checkbox" value="${content}">
      <div class="input-group-append">
        <button class="btn btn-outline-danger" type="button" value="${id}"><svg viewBox="0 0 30 30" class="trash" style="width: 17px; height: 17px; display: block; fill: inherit; flex-shrink: 0; backface-visibility: hidden;"><path d="M21,5c0-2.2-1.8-4-4-4h-4c-2.2,0-4,1.8-4,4H2v2h2v22h22V7h2V5H21z M13,3h4c1.104,0,2,0.897,2,2h-8C11,3.897,11.897,3,13,3zM24,27H6V7h18V27z M16,11h-2v12h2V11z M20,11h-2v12h2V11z M12,11h-2v12h2V11z"></path></svg></button>
      </div>
    </div>
  `;
}

const addTodo = function callback() {
  const content = $('.todoContent').val();
  const isCompleted = 0;
  $.ajax({
    type: 'POST',
    url: 'http://localhost:8080/K/todolist/list',
    data: {
      content,
      isCompleted,
    },
  }).done((res) => {
    const id = res.id;
    $('.list-group').append(getTodo(id, content, isCompleted));
  })
  $('.todoContent').val('');
}
