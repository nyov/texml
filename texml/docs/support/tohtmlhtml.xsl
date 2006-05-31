<x:stylesheet version="1.0" xmlns:x="http://www.w3.org/1999/XSL/Transform">
<!-- refines HTML produced by tohtml.xsl -->
<x:output method="html" encoding="iso-8859-1"/>

<x:template match="node()|@*">
	<x:copy>
		<x:apply-templates select="node()|@*" />
	</x:copy>
</x:template>

<x:template match="html/@id"/>

</x:stylesheet>
