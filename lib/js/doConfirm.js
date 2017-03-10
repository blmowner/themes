/**
 * Prompt user whenever the system need the confirmation from the user
 * such as delete operation and etc.
 * author : MJMZ a.k.a softboxkid
 * website : softboxkid.com
 * copyright: 2012
 */

function DoConfirm(message, url)
{
	if(confirm(message)) location.href = url;
}