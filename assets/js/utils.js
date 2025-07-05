/**
 * Formats a number to French style with 2 decimals.
 * @param {number} num - The number to format.
 * @returns {string} Formatted number (e.g. "1 234,50").
 */
export function formatNumber(num) {
  const fixedNumber = num.toFixed(2);
  const [integerPart, decimalPart] = fixedNumber.split(".");
  const formattedInteger = new Intl.NumberFormat("fr-FR").format(integerPart);

  return `${formattedInteger},${decimalPart}`;
}

/**
 * Parses a string to a number and returns it formatted in French style with 2 decimals.
 * Returns null if the input is not a valid number.
 * @param {string} numString - The string to parse and format.
 * @returns {string|null} Formatted number (e.g. "1 234,50") or null if invalid.
 */
export function formatNumberFromString(numString) {
  const parsedNum = parseFloat(numString);
  if (isNaN(parsedNum)) {
    return null;
  }

  const fixedNumber = parsedNum.toFixed(2);
  const [integerPart, decimalPart] = fixedNumber.split(".");
  const formattedInteger = new Intl.NumberFormat("fr-FR").format(integerPart);

  return `${formattedInteger},${decimalPart}`;
}

/**
 * Formats the value of an input field to ensure it contains only numbers and a single decimal point.
 * Additionally, it limits the number of digits after the decimal point to 2.
 * @param {HTMLInputElement} input - The input element to format.
 */
export function formatInputValue(input) {
  input.value = input.value
    .replace(/,/g, ".") // Replace commas with dots to standardize the decimal separator
    .replace(/[^\d.]/g, "") // Keep only digits and the decimal point
    .replace(/(\..*)\./g, "$1") // Ensure only one decimal point exists
    .replace(/(\.[\d]{2})./g, "$1") // Limit to 2 digits after the decimal point
    .replace(".", ","); // Replace the dot with a comma for display
  if (input.value.startsWith(",")) {
    input.value = "0" + input.value;
  }
}

export function clearLocalStorage(item) {
  localStorage.removeItem(item);
}

/**
 * Returns the French name of a month based on its number.
 * @param {number|string} month - The month number (1-12) as a number or string.
 * @returns {string} The full French name of the month.
 */
export function getFrenchMonthName(month) {
  const monthNames = [
    "janvier",
    "février",
    "mars",
    "avril",
    "mai",
    "juin",
    "juillet",
    "août",
    "septembre",
    "octobre",
    "novembre",
    "décembre",
  ];

  const monthIndex = Number(month) - 1;
  return monthNames[monthIndex] || "Mois invalide";
}
