class Greeter
{
  greet()
  {
    alert('Hello');
  }
}

let g = new Greeter();
document.addEventListener('DOMContentLoaded', () => {
  g.greet();
});