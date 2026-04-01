const savedTheme = localStorage.getItem('app-theme') || 'blue';
document.documentElement.setAttribute('data-theme', savedTheme);

// document.addEventListener("alpine:init", () => {
//     Alpine.store("config", {
//         typeModal: "",
//         typeModal_delete: "",
//         isEdit: 0,
//         isForceDelete: 0,
//         colorIcon: "",


//     });
// });