/**
 * SEO Meta composable for SPA
 * Manages <title>, <meta> tags, Open Graph, Twitter Cards, and JSON-LD
 */
import { useSiteStore } from '@/stores/site'

const SITE_NAME_DEFAULT = 'E-Commerce'

/**
 * Set or update a <meta> tag by name or property
 */
function setMeta(attr, attrValue, content) {
  if (!content) return
  let el = document.querySelector(`meta[${attr}="${attrValue}"]`)
  if (!el) {
    el = document.createElement('meta')
    el.setAttribute(attr, attrValue)
    document.head.appendChild(el)
  }
  el.setAttribute('content', content)
}

/**
 * Set JSON-LD script tag
 */
function setJsonLd(data) {
  // Remove old JSON-LD for this page
  const old = document.querySelector('script[data-seo-jsonld]')
  if (old) old.remove()

  if (!data) return
  const script = document.createElement('script')
  script.type = 'application/ld+json'
  script.setAttribute('data-seo-jsonld', 'true')
  script.textContent = JSON.stringify(data)
  document.head.appendChild(script)
}

/**
 * Main composable
 */
export function useSeoMeta() {
  const siteStore = useSiteStore()

  function getSiteName() {
    return siteStore.getSetting('site_name') || SITE_NAME_DEFAULT
  }

  function getBaseUrl() {
    return window.location.origin
  }

  /**
   * Set page SEO meta tags
   * @param {Object} options
   * @param {string} options.title - Page title (will be appended with site name)
   * @param {string} options.description - Meta description
   * @param {string} options.keywords - Meta keywords
   * @param {string} options.image - OG/Twitter image URL
   * @param {string} options.url - Canonical URL
   * @param {string} options.type - OG type (website, product, article)
   * @param {string} options.robots - Robots meta (index, noindex, follow, nofollow)
   */
  function setSeoMeta({
    title = '',
    description = '',
    keywords = '',
    image = '',
    url = '',
    type = 'website',
    robots = 'index, follow'
  } = {}) {
    const siteName = getSiteName()
    const baseUrl = getBaseUrl()
    const fullTitle = title ? `${title} - ${siteName}` : siteName
    const canonicalUrl = url || window.location.href
    const ogImage = image || `${baseUrl}/images/og-default.jpg`

    // Title
    document.title = fullTitle

    // Basic meta
    setMeta('name', 'description', description)
    setMeta('name', 'keywords', keywords)
    setMeta('name', 'robots', robots)

    // Canonical link
    let canonical = document.querySelector('link[rel="canonical"]')
    if (!canonical) {
      canonical = document.createElement('link')
      canonical.setAttribute('rel', 'canonical')
      document.head.appendChild(canonical)
    }
    canonical.setAttribute('href', canonicalUrl)

    // Open Graph
    setMeta('property', 'og:title', fullTitle)
    setMeta('property', 'og:description', description)
    setMeta('property', 'og:image', ogImage)
    setMeta('property', 'og:url', canonicalUrl)
    setMeta('property', 'og:type', type)
    setMeta('property', 'og:site_name', siteName)

    // Twitter Card
    setMeta('name', 'twitter:card', image ? 'summary_large_image' : 'summary')
    setMeta('name', 'twitter:title', fullTitle)
    setMeta('name', 'twitter:description', description)
    if (image) setMeta('name', 'twitter:image', ogImage)
  }

  /**
   * Set Product JSON-LD structured data
   */
  function setProductJsonLd(product) {
    if (!product) return
    const baseUrl = getBaseUrl()
    const siteName = getSiteName()
    const price = product.sale_price || product.regular_price || 0
    const imageUrl = product.images?.[0]?.url || product.image || `${baseUrl}/images/og-default.jpg`

    setJsonLd({
      '@context': 'https://schema.org',
      '@type': 'Product',
      name: product.name,
      description: product.short_description || product.description?.replace(/<[^>]*>/g, '').substring(0, 500),
      image: imageUrl.startsWith('http') ? imageUrl : `${baseUrl}${imageUrl}`,
      sku: product.sku || product.id,
      brand: product.brand ? { '@type': 'Brand', name: product.brand.name } : undefined,
      offers: {
        '@type': 'Offer',
        url: `${baseUrl}/product/${product.slug}`,
        priceCurrency: 'BDT',
        price: price,
        availability: product.stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        itemCondition: 'https://schema.org/NewCondition'
      },
      aggregateRating: product.rating_count > 0 ? {
        '@type': 'AggregateRating',
        ratingValue: product.rating || 5,
        reviewCount: product.rating_count
      } : undefined
    })
  }

  /**
   * Set BreadcrumbList JSON-LD
   */
  function setBreadcrumbJsonLd(items) {
    if (!items?.length) return
    const baseUrl = getBaseUrl()

    setJsonLd({
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: items.map((item, i) => ({
        '@type': 'ListItem',
        position: i + 1,
        name: item.name,
        item: item.url ? (item.url.startsWith('http') ? item.url : `${baseUrl}${item.url}`) : undefined
      }))
    })
  }

  /**
   * Set Organization JSON-LD
   */
  function setOrganizationJsonLd() {
    const baseUrl = getBaseUrl()
    const siteName = getSiteName()

    setJsonLd({
      '@context': 'https://schema.org',
      '@type': 'Organization',
      name: siteName,
      url: baseUrl,
      logo: `${baseUrl}/images/logo.png`
    })
  }

  /**
   * Clear all SEO meta (cleanup on unmount)
   */
  function clearSeoMeta() {
    setJsonLd(null)
  }

  return {
    setSeoMeta,
    setProductJsonLd,
    setBreadcrumbJsonLd,
    setOrganizationJsonLd,
    clearSeoMeta
  }
}
