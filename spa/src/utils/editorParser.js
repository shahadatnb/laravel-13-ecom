/**
 * Check if a string is Editor.js JSON data.
 */
export function isEditorJsData(content) {
  if (!content || typeof content !== 'string') return false
  try {
    const parsed = JSON.parse(content)
    return parsed && parsed.blocks && Array.isArray(parsed.blocks)
  } catch {
    return false
  }
}

/**
 * Convert Editor.js JSON string to HTML string.
 * Handles: paragraph, header, list, quote, checklist, delimiter, warning, image,
 *          simpleImage, table, code, embed, linkTool, mediaText
 */
export function editorJsToHtml(jsonString) {
  if (!jsonString) return ''

  let data
  try {
    data = JSON.parse(jsonString)
  } catch {
    return jsonString
  }

  if (!data || !data.blocks || !Array.isArray(data.blocks)) {
    return jsonString
  }

  return data.blocks.map(block => blockToHtml(block)).join('\n')
}

/**
 * Escape HTML entities in a string.
 */
function escapeHtml(str) {
  if (!str) return ''
  const div = document.createElement('div')
  div.appendChild(document.createTextNode(str))
  return div.innerHTML
}

/**
 * Convert a single Editor.js block to HTML.
 */
function blockToHtml(block) {
  const { type, data } = block
  if (!data) return ''

  switch (type) {
    case 'paragraph':
      return `<p>${data.text || ''}</p>`

    case 'header': {
      const level = data.level || 2
      return `<h${level}>${data.text || ''}</h${level}>`
    }

    case 'list': {
      const tag = data.style === 'ordered' ? 'ol' : 'ul'
      const items = (data.items || []).map(item => `<li>${item}</li>`).join('')
      return `<${tag}>${items}</${tag}>`
    }

    case 'quote':
      return `<blockquote><p>${data.text || ''}</p>${data.caption ? `<footer>— ${data.caption}</footer>` : ''}</blockquote>`

    case 'checklist': {
      const items = (data.items || []).map(item => {
        const checked = item.checked ? ' checked=""' : ''
        return `<div class="flex items-start gap-2 mb-1"><input type="checkbox"${checked} disabled class="mt-1"><span>${item.text || ''}</span></div>`
      }).join('')
      return `<div class="checklist">${items}</div>`
    }

    case 'image': {
      const fileUrl = data.file?.url || data.url || ''
      const caption = data.caption ? `<figcaption class="text-sm text-gray-500 text-center mt-2">${data.caption}</figcaption>` : ''
      const stretched = data.stretched ? ' w-full' : ''
      const withBorder = data.withBorder ? ' border border-gray-200' : ''
      const withBackground = data.withBackground ? ' bg-gray-50 p-2 rounded' : ''
      if (!fileUrl) return ''
      return `<figure class="my-6${stretched}${withBorder}${withBackground}">
        <img src="${fileUrl}" alt="${data.caption || ''}" class="max-w-full h-auto rounded-lg" loading="lazy">
        ${caption}
      </figure>`
    }

    case 'simpleImage': {
      const url = data.url || ''
      const caption = data.caption || ''
      const width = data.width || ''
      if (!url) return ''
      return `<figure class="my-6 text-center">
        <img src="${url}" alt="${caption}" class="rounded-lg max-w-full h-auto" style="${width ? `width: ${width}px` : ''}" loading="lazy">
        ${caption ? `<figcaption class="text-sm text-gray-500 text-center mt-2">${caption}</figcaption>` : ''}
      </figure>`
    }

    case 'delimiter':
      return '<hr class="my-6 border-gray-200">'

    case 'warning':
      return `<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4"><p class="font-semibold text-yellow-800">${data.title || ''}</p><p class="text-yellow-700">${data.message || ''}</p></div>`

    case 'table': {
      const content = data.content || []
      if (!content.length) return ''
      let html = '<div class="overflow-x-auto my-6"><table class="min-w-full border-collapse border border-gray-300">'
      content.forEach((row, rowIndex) => {
        const tag = rowIndex === 0 ? 'th' : 'td'
        html += '<tr>'
        ;(row || []).forEach(cell => {
          html += `<${tag} class="border border-gray-300 px-3 py-2 text-left">${cell || ''}</${tag}>`
        })
        html += '</tr>'
      })
      html += '</table></div>'
      return html
    }

    case 'code':
      return `<pre class="bg-gray-900 text-gray-100 rounded-lg p-4 my-4 overflow-x-auto text-sm"><code>${escapeHtml(data.code || '')}</code></pre>`

    case 'embed': {
      const { service, embed: embedUrl, width, height, caption } = data
      if (!embedUrl) return ''
      if (service === 'youtube') {
        // YouTube embeds: use responsive iframe
        return `<div class="aspect-w-16 aspect-h-9 my-6">` +
          `<iframe src="${embedUrl}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full rounded-lg" style="aspect-ratio: 16/9"></iframe>` +
          `</div>`
      }
      // Generic embed
      return `<div class="my-4">` +
        `<iframe src="${embedUrl}" width="${width || '100%'}" height="${height || 400}" frameborder="0" allowfullscreen class="rounded-lg mx-auto"></iframe>` +
        `${caption ? `<p class="text-sm text-gray-500 text-center mt-1">${caption}</p>` : ''}` +
        `</div>`
    }

    case 'linkTool': {
      const { link, meta } = data
      if (!link) return ''
      const title = meta?.title || link
      const description = meta?.description || ''
      const image = meta?.image?.url || meta?.image || ''
      return `<a href="${link}" target="_blank" rel="noopener noreferrer" class="block border border-gray-200 rounded-lg my-4 overflow-hidden hover:shadow-md transition-shadow no-underline text-inherit">` +
        (image ? `<div class="w-full h-48 bg-gray-100 overflow-hidden"><img src="${image}" alt="${title}" class="w-full h-full object-cover" loading="lazy"></div>` : '') +
        `<div class="p-4">` +
        `<h4 class="font-semibold text-gray-900 mb-1">${title}</h4>` +
        (description ? `<p class="text-sm text-gray-600 line-clamp-2">${description}</p>` : '') +
        `<span class="text-xs text-gray-400 mt-2 block truncate">${link}</span>` +
        `</div></a>`
    }

    case 'mediaText': {
      const mediaUrl = data.mediaUrl || ''
      const title = data.title || ''
      const text = data.text || ''
      const align = data.align || 'left'
      const orderClasses = align === 'right' ? 'flex-col-reverse md:flex-row-reverse' : 'flex-col md:flex-row'
      if (!mediaUrl && !title && !text) return ''
      return `<div class="flex ${orderClasses} gap-6 my-8 items-start">
        ${mediaUrl ? `<div class="flex-1 md:flex-none md:w-1/2">
          <img src="${mediaUrl}" alt="${title}" class="w-full h-auto rounded-lg shadow-sm" loading="lazy">
        </div>` : ''}
        <div class="flex-1">
          ${title ? `<h3 class="text-xl font-bold text-gray-900 mb-3">${escapeHtml(title)}</h3>` : ''}
          ${text ? `<div class="prose prose-gray max-w-none">${text}</div>` : ''}
        </div>
      </div>`
    }

    default:
      if (data.text) return `<p>${data.text}</p>`
      if (data.html) return data.html
      return ''
  }
}
