<div class="oAuthService">
    <?php if (isset($googleUserInfo) && $googleUserInfo): ?>
        <div class="avatar" style="background-image: url('<?=$googleUserInfo['avatar'];?>');"></div>
        Logged In With <span class="service">Google</span>
        <br>
        <br> Name: <?=$googleUserInfo['name'];?>
        <br> Email: <?=$googleUserInfo['email'];?>
        <br> Id: <?=$googleUserInfo['uid'];?>
        <br>
        <br> <a href="core/logout.php?service=google" class="btn">Logout</a>
    <?php elseif (isset($googleLoginUrl) && $googleLoginUrl): ?>
        <br> <a href="<?=$googleLoginUrl;?>" id="googleOAuth" class='google_login'></a>
    <?php else: ?>
        <br> Failed to generate Google sign in URL.
    <?php endif?>
</div>

<div class="oAuthService">
    <?php if (isset($facebookUserInfo) && $facebookUserInfo): ?>
        <div class="avatar" style="background-image: url('<?=$facebookUserInfo['avatar'];?>');"></div>
        Logged In With <span class="service">Facebook</span>
        <br>
        <br> Name: <?=$facebookUserInfo['name'];?>
        <br> Email: <?=$facebookUserInfo['email'];?>
        <br> Id: <?=$facebookUserInfo['uid'];?>
        <br>
        <br> <a href="core/logout.php?service=facebook" class="btn">Logout</a>
    <?php elseif (isset($facebookLoginUrl) && $facebookLoginUrl): ?>
        <br> <a href="<?=$facebookLoginUrl;?>" id="facebookOAuth" class='facebook_login'></a>
    <?php else: ?>
        <br> Failed to generate Facebook sign in URL.
    <?php endif?>
</div>

<div class="oAuthService">
    <?php if (isset($twitterUserInfo) && $twitterUserInfo): ?>
        <div class="avatar" style="background-image: url('<?=$twitterUserInfo['avatar'];?>');"></div>
        Logged In With <span class="service">Twitter</span>
        <br>
        <br> Name: <?=$twitterUserInfo['name'];?>
        <br> Email: <?=$twitterUserInfo['email'];?>
        <br> Id: <?=$twitterUserInfo['uid'];?>
        <br>
        <br> <a href="core/logout.php?service=twitter" class="btn">Logout</a>
    <?php elseif (isset($twitterLoginUrl) && $twitterLoginUrl): ?>
        <br> <a href="<?=$twitterLoginUrl;?>" id="twitterOAuth" class='twitter_login'></a>
    <?php else: ?>
        <br> Failed to generate Twitter sign in URL.
    <?php endif?>
</div>

<div class="oAuthService">
    <?php if (isset($linkedinUserInfo) && $linkedinUserInfo): ?>
        <div class="avatar" style="background-image: url('<?=$linkedinUserInfo['avatar'];?>');"></div>
        Logged In With <span class="service">Linkedin</span>
        <br>
        <br> Name: <?=$linkedinUserInfo['name'];?>
        <br> Email: <?=$linkedinUserInfo['email'];?>
        <br> Id: <?=$linkedinUserInfo['uid'];?>
        <br>
        <br> <a href="core/logout.php?service=linkedin" class="btn">Logout</a>
    <?php elseif (isset($linkedinLoginUrl) && $linkedinLoginUrl): ?>
        <br> <a href="<?=$linkedinLoginUrl;?>" id="linkedinOAuth" class='linkedin_login'></a>
    <?php else: ?>
        <br> Failed to generate Linkedin sign in URL.
    <?php endif?>
</div>

<div class="oAuthService">
    <?php if (isset($microsoftUserInfo) && $microsoftUserInfo): ?>
        <div class="avatar" style="background-image: url('<?=$microsoftUserInfo['avatar'];?>');"></div>
        Logged In With <span class="service">Microsoft</span>
        <br>
        <br> Name: <?=$microsoftUserInfo['name'];?>
        <br> Email: <?=$microsoftUserInfo['email'];?>
        <br> Id: <?=$microsoftUserInfo['uid'];?>
        <br>
        <br> <a href="core/logout.php?service=microsoft" class="btn">Logout</a>
    <?php elseif (isset($microsoftLoginUrl) && $microsoftLoginUrl): ?>
        <br> <a href="<?=$microsoftLoginUrl;?>" id="microsoftOAuth" class='microsoft_login'></a>
    <?php else: ?>
        <br> Failed to generate Microsoft sign in URL.
    <?php endif?>
</div>

<div class="oAuthService">
    <?php if (isset($yahooUserInfo) && $yahooUserInfo): ?>
        <div class="avatar" style="background-image: url('<?=$yahooUserInfo['avatar'];?>');"></div>
        Logged In With <span class="service">Yahoo</span>
        <br>
        <br> Name: <?=$yahooUserInfo['name'];?>
        <br> Email: <?=$yahooUserInfo['email'];?>
        <br> Id: <?=$yahooUserInfo['uid'];?>
        <br>
        <br> <a href="core/logout.php?service=yahoo" class="btn">Logout</a>
    <?php elseif (isset($yahooLoginUrl) && $yahooLoginUrl): ?>
        <br> <a href="<?=$yahooLoginUrl;?>" id="yahooOAuth" class='yahoo_login'></a>&nbsp;&nbsp;&nbsp;(<span class="service">Yahoo</span>: Try this on 127.0.0.1, doesn't work on localhost domain).
    <?php else: ?>
        <br> Failed to generate Yahoo sign in URL.
    <?php endif?>
</div>