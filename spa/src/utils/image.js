/**
 * Convert an image path to a URL served from Laravel's /storage directory.
 * Returns a relative path so it works on any domain (localhost or production).
 *
 * @param {string|null} path - The image path (e.g. 'products/image.jpg', '/storage/products/image.jpg')
 * @param {string} fallback - Fallback image path if the given path is empty
 * @returns {string} URL path
 */
export function getImageUrl(path, fallback = '/assets/placeholder.svg') {
  // Use fallback if path is empty
  const imagePath = path || fallback

  // If it's already a full URL, return as-is
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
    return imagePath
  }

  // Ensure path starts with /
  let normalizedPath = imagePath.startsWith('/') ? imagePath : `/${imagePath}`

  // Prepend /storage for non-asset paths (unless it already has /storage or /assets)
  if (!normalizedPath.startsWith('/storage/') && !normalizedPath.startsWith('/assets/')) {
    normalizedPath = `/storage${normalizedPath}`
  }

  return normalizedPath
}
