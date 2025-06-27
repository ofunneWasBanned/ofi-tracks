const authModal = document.querySelector(".auth-modal");
const loginLink = document.querySelector(".login-link");
const registerLink = document.querySelector(".register-link");

registerLink.addEventListener("click", () => authModal.classList.add("slide"));
loginLink.addEventListener("click", () => authModal.classList.remove("slide"));

const loginBtnModal = document.querySelector(".login-btn-modal");
const closeBtnModal = document.querySelector(".close-btn-modal");

if (loginBtnModal) loginBtnModal.addEventListener("click", () => {
  authModal.classList.toggle("show")
});

closeBtnModal.addEventListener("click", () => authModal.classList.remove("show", "slide"));

const profileBox = document.querySelector(".profile-box");
const profileCircle = document.querySelector(".profile-circle");
const dropdown = document.querySelector(".dropdown");

if (profileCircle) profileCircle.addEventListener("click", () => profileBox.classList.toggle("show"));

const alertBox = document.querySelector(".alert-box");

if (alertBox) {
  setTimeout(() => alertBox.classList.add("show"), 50);

  setTimeout(() => {
    alertBox.classList.remove("show");
    setTimeout(() => alertBox.remove(), 1000);
  }, 3000)
}

const balanceText = document.querySelector(".balance-text");

const cards = document.querySelector(".cards");
const cardContainer = document.querySelector(".container");
const balanceCard = document.querySelector(".balance-card");
const clickInfo = document.querySelector(".click-info");
const clickedInfo = document.querySelector(".clicked-info");
const notLoggedInfo = document.querySelector(".not-logged-info");

//const expansion = document.querySelector("#expansion");

if (balanceCard) balanceCard.addEventListener("click", ()=>{
  cards.classList.toggle("expanded");
  clickInfo.classList.toggle("expanded");
  clickedInfo.classList.toggle("expanded");
});

//loginBtnModal.addEventListener("click", () => {
//  cards.classList.remove("expanded");
//}) 

//balanceCard.addEventListener("click", ()=>{
//  cards.classList.toggle("expanded");

//  setTimeout(()=>{
//    cardContainer.style.height = cards.classList.contains("expanded")
//    ? `${cards.scrollHeight}px`
//    : `${balanceCard.offsetHeight}px`;
//  }, 10)
//});


// expense functions

/*

"use strict";

const exErrorMessageEL = document.querySelector(".expense-error");
const inErrorMessageEL = document.querySelector(".income-error");
const expenseCat = document.querySelector(".expense-cat");
const incomeCat = document.querySelector(".income-cat");
const incomeInput = document.querySelector(".income-input");
const expenseInput = document.querySelector(".expense-input");
const incomeAmount = document.querySelector(".income-amount");
const expenseAmount = document.querySelector(".expense-amount");
const incomeContent = document.querySelector(".income-content");
const expenseContent = document.querySelector(".expense-content");
const yourIncome = document.querySelector(".your-income");
const yourExpenses = document.querySelector(".your-expenses");
const exContainer = document.querySelector(".ex-container");
const inContainer = document.querySelector(".in-container")

// table 
const exTableData = document.querySelector(".ex-table-data");
const exTableContent = document.querySelector(".ex-table-content");
const inTableData = document.querySelector(".in-table-data");
const inTableContent = document.querySelector(".in-table-content");

// cards 
const incomeCard = document.querySelector(".income-card");
const expenseCard = document.querySelector(".expense-card");
*/
/* let exItemList = [];
let exItemId = 0;

let inItemList = [];
let inItemId = 0; */

/*

// Button events

function buttonEvents(){
  const incomeCalc = document.querySelector("#income-button");
  const expenseCalc = document.querySelector("#expense-button");

  // income event

  if (incomeCalc) incomeCalc.addEventListener("click", (e)=>{
  e.preventDefault();
  incomeFunc();
  })

  // expenses event
  
  if (expenseCalc) expenseCalc.addEventListener("click", (e)=>{
  e.preventDefault();
  expenseFunc();
  })

}

// calling buttons event

document.addEventListener("DOMContentLoaded", buttonEvents);
*/
/*
// expenses function

function expenseFunc(){
  console.log("expenseFunc called");

  let expenseInputValue = expenseInput.value;
  let expenseAmountValue = expenseAmount.value;
  let expenseCatValue = expenseCat.value;

  if(expenseInputValue == "" || expenseAmountValue == "" || expenseCatValue == "" || parseInt(expenseAmount) < 0){
    exErrorMessage("Please Enter Expense Info | Expense Amount Must Be More Than 0")
  } else {
    let exAmount = parseFloat(expenseAmountValue);

    expenseAmount.value="";
    expenseInput.value="";
    expenseCat.value="";

    // store value inside object

    let expense = {
      id: exItemId,
      category: expenseCatValue,
      title: expenseInputValue,
      amount: exAmount,
    };

    console.log(expense);

    exItemId++
    exItemList.push(expense);

    // add expenses

    addExpense(expense);
    console.log("Expense added to DOM");
    showBalance();

  }
}

// income function

function incomeFunc(){
  let incomeInputValue = incomeInput.value;
  let incomeAmountValue = incomeAmount.value;
  let incomeCatValue = incomeCat.value;

  if(incomeInputValue == "" || incomeAmountValue == "" || incomeCatValue == "" || parseInt(incomeAmount) < 0){
    inErrorMessage("Please Enter Income Info | Income Amount Must Be More Than 0")
  } else {
    let inAmount = parseFloat(incomeAmountValue);

    incomeAmount.value="";
    incomeInput.value="";
    incomeCat.value="";

    // store value inside object

    let income = {
      id: inItemId,
      category: incomeCatValue,
      title: incomeInputValue,
      amount: inAmount,
    };

    inItemId++
    inItemList.push(income);

    // add income

    addIncome(income);
    showBalance();

  }
}
  */

// add expenses function
/* 

function addExpense(expensePara){
  const exFormattedAmount = parseFloat(expensePara.amount).toFixed(2);

  const exHtml = `<ul class="ex-table-tr-content" data-id="${expensePara.id}">
                  <li data-category=${expensePara.category}>${expensePara.category}</li>
                  <li data-title=${expensePara.title}>${expensePara.title}</li>
                  <li data-amount=${exFormattedAmount}><span>£</span>${exFormattedAmount}</li>
                  <li>
                    <button type="button" class="ex-button-edit">
                      Edit
                    </button>
                    <button type="button" class="ex-button-delete">
                      Delete
                    </button>
                  </li>
                </ul>`;
  
  exTableData.insertAdjacentHTML("beforeend", exHtml)
}

// Expense Edit/Delete Event Delegation
if (exTableData) exTableData.addEventListener("click", function (e) {
  const target = e.target;

  // Handle Edit button
  if (target.classList.contains("ex-button-edit")) {
    const element = target.closest("ul");
    const id = parseInt(element.querySelector("li").dataset.id);

    // Remove element from DOM
    element.remove();

    // Get the item data and repopulate the form
    const expense = exItemList.find(item => item.id === id);
    if (expense) {
      expenseInput.value = expense.title;
      expenseAmount.value = expense.amount;
      expenseCat.value = expense.category;
    }

    // Remove from array
    exItemList = exItemList.filter(item => item.id !== id);

    showBalance();
  }

  // Handle Delete button
  if (target.classList.contains("ex-button-delete")) {
    const element = target.closest("ul");
    const id = parseInt(element.querySelector("li").dataset.id);

    // Remove element from DOM
    element.remove();

    // Remove from array
    exItemList = exItemList.filter(item => item.id !== id);

    showBalance();
  }
});
*/
/*
// add income function

function addIncome(incomePara){
  const inFormattedAmount = parseFloat(incomePara.amount).toFixed(2);

  const inHtml = `<ul class="in-table-tr-content" data-id="${incomePara.id}">
                  <li data-category=${incomePara.category}>${incomePara.category}</li>
                  <li data-title=${incomePara.title}>${incomePara.title}</li>
                  <li data-amount=${inFormattedAmount}><span>£</span>${inFormattedAmount}</li>
                  <li>
                    <button type="button" class="in-button-edit">
                      Edit
                    </button>
                    <button type="button" class="in-button-delete">
                      Delete
                    </button>
                  </li>
                </ul>`;
  
  inTableData.insertAdjacentHTML("beforeend", inHtml)
}

// Income Edit/Delete Event Delegation
if (inTableData) inTableData.addEventListener("click", function (e) {
  const target = e.target;

  // Handle Edit button
  if (target.classList.contains("in-button-edit")) {
    const element = target.closest("ul");
    const id = parseInt(element.querySelector("li").dataset.id);

    // Remove element from DOM
    element.remove();

    // Get the item data and repopulate the form
    const expense = inItemList.find(item => item.id === id);
    if (income) {
      incomeInput.value = income.title;
      incomeAmount.value = income.amount;
      incomeCat.value = income.category;
    }

    // Remove from array
    inItemList = inItemList.filter(item => item.id !== id);

    showBalance();
  }

  // Handle Delete button
  if (target.classList.contains("in-button-delete")) {
    const element = target.closest("ul");
    const id = parseInt(element.querySelector("li").dataset.id);

    // Remove element from DOM
    element.remove();

    // Remove from array
    inItemList = inItemList.filter(item => item.id !== id);

    showBalance();
  }
});
*/

  // edit/delete

  //const exEditButton = document.querySelectorAll(".ex-button-edit");
  //const exDeleteButton = document.querySelectorAll(".ex-button-delete");
  //const inEditButton = document.querySelectorAll(".in-button-edit");
  //const inDeleteButton = document.querySelectorAll(".in-button-delete");
  //const exContentId = document.querySelectorAll(".ex-table-tr-content");
  //const inContentId = document.querySelectorAll(".in-table-tr-content");

  //  expense button edit event

  /*exEditButton.forEach((exbuttonedit)=>{
    exbuttonedit.addEventListener("click", (el)=>{
      //let id;

      //exContentId.forEach((ids)=>{
      //  id = ids.firstElementChild.dataset.id;
      //});

      //let element = el.target.parentElement.parentElement

      element.remove();

      const element = el.target.closest("ul");
      const id = element.querySelector("li").dataset.id;

      let expense = exItemList.filter(function(item){
        return item.id == Number(id);
      });

      expenseInput.value = expense[0].title;
      expenseAmount.value = expense[0].amount;

      let tempList = exItemList.filter(function(item){
        return item.id != id;
      });

      exItemList = tempList;

    });
  });

  // income button edit event

  inEditButton.forEach((inbuttonedit)=>{
    inbuttonedit.addEventListener("click", (el)=>{
      //let id;

      //inContentId.forEach((ids)=>{
      //  id = ids.firstElementChild.dataset.id;
      //});

      //let element = el.target.parentElement.parentElement
      element.remove();

      const element = el.target.closest("ul");
      const id = element.querySelector("li").dataset.id;


      let income = inItemList.filter(function(item){
        return item.id == Number(id);
      });

      incomeInput.value = income[0].title;
      incomeAmount.value = income[0].amount;

      let tempList = inItemList.filter(function(item){
        return item.id != id;
      });

      inItemList = tempList;

    });
  });

  // expense button delete event

  exDeleteButton.forEach((exbuttondelete)=>{
    exbuttondelete.addEventListener("click", (el)=>{
      let id;

      exContentId.forEach((ids)=>{
        id = ids.firstElementChild.dataset.id;
      })

      let element = el.target.parentElement.parentElement
      element.remove();

      let tempList = exItemList.filter(function(item){
        return item.id != id;
      });

      exItemList = tempList;
      showBalance();

    });
  });

  // income button delete event

  inDeleteButton.forEach((inbuttondelete)=>{
    inbuttondelete.addEventListener("click", (el)=>{
      let id;

      inContentId.forEach((ids)=>{
        id = ids.firstElementChild.dataset.id;
      })

      let element = el.target.parentElement.parentElement
      element.remove();

      let tempList = inItemList.filter(function(item){
        return item.id != id;
      });

      inItemList = tempList;
      showBalance();

    });
  });
  */

/*
// show balance

function showBalance(){
  const expense = expenseTotal();
  const income = incomeTotal();
  const total = income - expense;
  balanceCard.textContent = total.toFixed(2);
}

// expense total 

function expenseTotal(){
  
  let total = 0;
  
  if (exItemList.length > 0){
    total = exItemList.reduce(function(acc,curr){
      acc += curr.amount;
      return acc;
    }, 0)
  }

  expenseCard.textContent = total.toFixed(2);

  return total;

}

// income total 

function incomeTotal(){
  
  let total = 0;
  
  if (inItemList.length > 0){
    total = inItemList.reduce(function(acc,curr){
      acc += curr.amount;
      return acc;
    }, 0)
  }

  incomeCard.textContent = total.toFixed(2);

  return total;

}
  */
/*
// error message function

function exErrorMessage(message){
  exErrorMessageEL.innerHTML = `<p>${message}</p>` ;
    exErrorMessageEL.classList.add("error");
    setTimeout(() => {
      exErrorMessageEL.classList.remove("error");
    }, 3000);
}

function inErrorMessage(message){
  inErrorMessageEL.innerHTML = `<p>${message}</p>` ;
    inErrorMessageEL.classList.add("error");
    setTimeout(() => {
      inErrorMessageEL.classList.remove("error");
    }, 3000);
}
    */