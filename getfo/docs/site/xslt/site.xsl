<x:stylesheet version="1.0" xmlns:x="http://www.w3.org/1999/XSL/Transform">
<!-- -->
<x:output method="html" />

<x:variable name="docid" select="/html/@id"/>

<x:template match="node()|@*">
	<x:copy>
		<x:apply-templates select="node()|@*"/>
	</x:copy>
</x:template>

<x:template match="body">
	<body>
		<x:apply-templates select="document('inc/menu.xml',/)"/>
		<x:apply-templates select="node()|@*"/>
	</body>
</x:template>

<x:template match="menu">
	<x:apply-templates select="node()|@*"/>
	<hr />
</x:template>

<x:template match="menuitem">
	<x:text>[</x:text>
	<x:choose>
		<x:when test="@id=$docid">
			<x:value-of select="."/>
		</x:when>
		<x:otherwise>
			<a href="{concat(@id,'.html')}">
				<x:value-of select="."/>
			</a>
		</x:otherwise>
	</x:choose>
	<x:text>]</x:text>	
</x:template>

</x:stylesheet>
