export function formatNumber(num) {
  console.log("hello la fonction qui formatte le nombre!");

  const fixedNumber = num.toFixed(2);
  const [integerPart, decimalPart] = fixedNumber.split(".");
  const formattedInteger = new Intl.NumberFormat("fr-FR").format(integerPart);

  return `${formattedInteger},${decimalPart}`;
}
