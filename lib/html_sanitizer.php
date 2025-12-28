<?php
/**
 * HTML Sanitizer for CBT System
 * 
 * Sanitizes HTML content while preserving rich formatting required by the system:
 * - Images, tables, lists, text formatting, math formulas
 * 
 * Removes dangerous elements:
 * - onclick, onerror, onload, and all on* event handlers
 * - javascript:, data:, vbscript: URI schemes
 * 
 * Safe for use before storage and before rendering (defense-in-depth)
 */

/**
 * Sanitize HTML content for safe storage and rendering
 * 
 * @param string $html Raw HTML content (may be entity-encoded)
 * @return string Sanitized HTML (entity-encoded for storage compatibility)
 */
function sanitizeHtmlContent($html) {
    if (empty($html) || !is_string($html)) {
        return '';
    }
    
    // Decode HTML entities first to work with actual HTML
    $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // If decoding didn't produce valid HTML, return empty string
    if (empty($decoded)) {
        return '';
    }
    
    // Use DOMDocument for safe HTML parsing
    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    
    // Wrap content in a container div to handle fragments
    $wrapped = '<div>' . $decoded . '</div>';
    
    // Load HTML with UTF-8 encoding
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
    // Clear libxml errors
    libxml_clear_errors();
    
    if (!$dom) {
        // Fallback: return empty string if parsing fails
        return '';
    }
    
    // Get the container div
    $container = $dom->getElementsByTagName('div')->item(0);
    if (!$container) {
        return '';
    }
    
    // Sanitize the DOM tree
    sanitizeNode($container);
    
    // Extract sanitized content
    $sanitized = '';
    foreach ($container->childNodes as $child) {
        $sanitized .= $dom->saveHTML($child);
    }
    
    // Re-encode to entities for storage compatibility
    return htmlentities($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Recursively sanitize DOM nodes
 * 
 * @param DOMNode $node Node to sanitize
 */
function sanitizeNode($node) {
    if ($node->nodeType === XML_ELEMENT_NODE) {
        $tagName = strtolower($node->nodeName);
        
        // List of allowed HTML tags
        $allowedTags = [
            'p', 'br', 'div', 'span', 'hr',
            'b', 'i', 'u', 's', 'strong', 'em', 'sub', 'sup',
            'ul', 'ol', 'li',
            'table', 'thead', 'tbody', 'tr', 'td', 'th',
            'img', 'a',
            // Math formula elements (WIRIS may generate these)
            'math', 'mi', 'mo', 'mn', 'mfrac', 'msup', 'msub', 'munderover',
            'mover', 'munder', 'mroot', 'mtable', 'mtr', 'mtd', 'mtext',
            'semantics', 'annotation', 'annotation-xml'
        ];
        
        // Remove disallowed tags
        if (!in_array($tagName, $allowedTags)) {
            // Replace with its children
            while ($node->firstChild) {
                $child = $node->firstChild;
                $node->parentNode->insertBefore($child, $node);
            }
            $node->parentNode->removeChild($node);
            return;
        }
        
        // Sanitize attributes
        $attributesToRemove = [];
        $attributesToKeep = [];
        
        foreach ($node->attributes as $attr) {
            $attrName = strtolower($attr->name);
            $attrValue = $attr->value;
            
            // Remove all event handlers (onclick, onerror, onload, etc.)
            if (preg_match('/^on/i', $attrName)) {
                $attributesToRemove[] = $attrName;
                continue;
            }
            
            // Handle specific attributes
            switch ($attrName) {
                case 'href':
                case 'src':
                    // Validate URI scheme - only allow http, https
                    if (preg_match('/^(https?:\/\/|\/|#)/i', $attrValue)) {
                        // Remove javascript:, data:, vbscript: schemes
                        $attrValue = preg_replace('/^(javascript|data|vbscript):/i', '', $attrValue);
                        $attributesToKeep[$attrName] = $attrValue;
                    } else {
                        // Remove dangerous URIs
                        $attributesToRemove[] = $attrName;
                    }
                    break;
                    
                case 'style':
                    // Allow style but sanitize it (remove dangerous CSS)
                    $sanitizedStyle = sanitizeStyle($attrValue);
                    if (!empty($sanitizedStyle)) {
                        $attributesToKeep[$attrName] = $sanitizedStyle;
                    }
                    break;
                    
                case 'class':
                case 'id':
                case 'alt':
                case 'title':
                case 'width':
                case 'height':
                case 'border':
                case 'target':
                case 'name':
                    // Allow safe attributes
                    $attributesToKeep[$attrName] = $attrValue;
                    break;
                    
                default:
                    // Remove unknown attributes
                    $attributesToRemove[] = $attrName;
                    break;
            }
        }
        
        // Remove dangerous attributes
        foreach ($attributesToRemove as $attrName) {
            $node->removeAttribute($attrName);
        }
        
        // Update allowed attributes
        foreach ($attributesToKeep as $attrName => $attrValue) {
            $node->setAttribute($attrName, $attrValue);
        }
    }
    
    // Recursively sanitize children
    $children = [];
    foreach ($node->childNodes as $child) {
        $children[] = $child;
    }
    
    foreach ($children as $child) {
        sanitizeNode($child);
    }
}

/**
 * Sanitize CSS style attribute
 * 
 * @param string $style Raw CSS
 * @return string Sanitized CSS
 */
function sanitizeStyle($style) {
    if (empty($style)) {
        return '';
    }
    
    // Remove dangerous CSS (expression, javascript:, behavior)
    $dangerous = [
        '/expression\s*\(/i',
        '/javascript\s*:/i',
        '/@import/i',
        '/behavior\s*:/i',
        '/binding\s*:/i',
        '/-moz-binding/i'
    ];
    
    $sanitized = $style;
    foreach ($dangerous as $pattern) {
        $sanitized = preg_replace($pattern, '', $sanitized);
    }
    
    return trim($sanitized);
}

/**
 * Sanitize HTML for rendering (after entity decode)
 * This is a wrapper that ensures content is safe for output
 * 
 * @param string $html HTML content (already decoded)
 * @return string Sanitized HTML ready for output
 */
function sanitizeHtmlForRender($html) {
    if (empty($html) || !is_string($html)) {
        return '';
    }
    
    // If already decoded, encode temporarily for sanitization
    $encoded = htmlentities($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $sanitized = sanitizeHtmlContent($encoded);
    
    // Decode for rendering
    return html_entity_decode($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

?>

