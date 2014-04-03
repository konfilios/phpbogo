Modifications made to original facebook sdk source code files

1) All classes were namespaced to Bogo\Facebook\Connector
2) Class BaseFacebook of original base_facebook.php file was renamed to Bogo\Facebook\Connector\Base and stored into its own file
3) Class FacebookApiException of original base_facebook.php file was renamed to Bogo\Facebook\Connector\ApiException and stored into its own class file
4) Class Facebook of original facebook.php file was renamed to Bogo\Facebook\Connector\Standard and stored into its own class file
