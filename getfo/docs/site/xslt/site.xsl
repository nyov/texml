<x:stylesheet version="1.0" xmlns:x="http://www.w3.org/1999/XSL/Transform">
<!-- -->

<x:template match="node()|@*">
	<x:copy>
		<x:apply-templates select="node()|@*"/>
	</x:copy>
</x:template>

</x:stylesheet>
