export function formatNumber(num) {
  console.log("hello la fonction qui formatte le nombre!");

  const fixedNumber = num.toFixed(2);
  const [integerPart, decimalPart] = fixedNumber.split(".");
  const formattedInteger = new Intl.NumberFormat("fr-FR").format(integerPart);

  return `${formattedInteger},${decimalPart}`;
}

export function formatInputValue(input) {
  input.value = input.value
    .replace(/[^\d.]/g, "") // Conserve uniquement les chiffres et le point
    .replace(/(^[\d]{4})[\d]/g, "$1") // Pas plus de 4 chiffres au début
    .replace(/(\..*)\./g, "$1") // Un seul point décimal autorisé
    .replace(/(\.[\d]{2})./g, "$1"); // Pas plus de 2 chiffres après le point décimal
}
