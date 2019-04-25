Auther is a project for provide kind of "user-directory".

### Remember: This is still WIP and therefore unreleased

The problem i often hear about, is that people dislike to remember various Accounts.

The solution i try to do is similiar to a lot of systems out there (one i've seen is rockstar's system for example) - one auth-server, all the apps using this one. 

Some pro's about a such structure are:

- Apps are distribute-able over various servers -> scaling
- Apps can be focused more on handle theyr task
- Various technologies (eg Django and PHP) can be "mixed" properly over the OAuth-Interface (and as separate apps)

To stay flexible, the project has only generic useable features, such as:

- Provide basic profile
- Provide notification-exchange (unfinished)
- 2-factor-auth (TOTP)
- Provide Login via Google, Github, Gitlab, Bitbucket, Twitter and Linkedin (if enabled by admin)
- Provide "Projects" for presenting the apps 
- Role and level-based access-system which can be used by apps
- Friend-system which can also be used by apps



### Setup and updates

As Auther is kind of similiar to LaraTube, it's also on setup. Checkout installation-instructions here: https://gitlab.com/hersche/LaraTube/wikis/SysAdminManual