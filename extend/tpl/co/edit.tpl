{extend name="common/co/edit" /}

{block name="header"}
	{include file="common/co/header" /}
{/block}
{block name="content"}
	{include file="common/co/information" /}
	{if ($conf.co.info == true)}
	{include file="common/co/info" /}
	{/if}
	{if ($conf.co.file == true)}
	{include file="common/co/file" /}
	{/if}
	{if ($conf.co.seo == true)}
	{include file="common/co/seo" /}
	{/if}
{/block}
{block name="script"}
{/block}