// Currency formatting utility
// This will be used with site settings from the API

let currencySettings = {
  symbol: '৳',
  code: 'BDT',
  position: 'before',
  decimals: 2,
  thousandSeparator: ',',
  decimalSeparator: '.'
}

/**
 * Initialize currency settings from API response
 * @param {Object} settings - Settings object from API
 */
export function initCurrencySettings(settings) {
  if (settings) {
    currencySettings.symbol = settings.currency_symbol || '৳'
    currencySettings.code = settings.currency_code || 'BDT'
    currencySettings.position = settings.currency_position || 'before'
    currencySettings.decimals = parseInt(settings.currency_decimals) || 2
    currencySettings.thousandSeparator = settings.currency_thousand_separator || ','
    currencySettings.decimalSeparator = settings.currency_decimal_separator || '.'
  }
}

/**
 * Format a price with currency symbol
 * @param {number} amount - The amount to format
 * @param {Object} options - Optional override settings
 * @returns {string} Formatted price string
 */
export function formatPrice(amount, options = {}) {
  if (amount === null || amount === undefined || amount === '') {
    return ''
  }

  // Convert to number if it's a string
  const numAmount = typeof amount === 'string' ? parseFloat(amount) : Number(amount)
  
  if (isNaN(numAmount)) {
    return ''
  }

  const settings = { ...currencySettings, ...options }
  const decimals = settings.decimals
  const decimalSep = settings.decimalSeparator
  const thousandSep = settings.thousandSeparator

  // Format the number
  const fixed = numAmount.toFixed(decimals)
  const [integerPart, decimalPart] = fixed.split('.')

  // Add thousand separators
  const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSep)

  // Build the final string
  const formattedAmount = decimalPart !== undefined
    ? `${formattedInteger}${decimalSep}${decimalPart}`
    : formattedInteger

  // Add currency symbol based on position
  if (settings.position === 'after') {
    return `${formattedAmount} ${settings.symbol}`
  }
  return `${settings.symbol}${formattedAmount}`
}

/**
 * Format a price range (min - max)
 * @param {number} min - Minimum amount
 * @param {number} max - Maximum amount
 * @returns {string} Formatted price range
 */
export function formatPriceRange(min, max) {
  return `${formatPrice(min)} - ${formatPrice(max)}`
}

/**
 * Get just the currency symbol
 * @returns {string} Currency symbol
 */
export function getCurrencySymbol() {
  return currencySettings.symbol
}

/**
 * Get currency code
 * @returns {string} Currency code
 */
export function getCurrencyCode() {
  return currencySettings.code
}

export default {
  initCurrencySettings,
  formatPrice,
  formatPriceRange,
  getCurrencySymbol,
  getCurrencyCode
}