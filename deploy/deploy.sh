#!/usr/bin/env bash

# @Author: Sergey Kuzmich
# @Date:   2017-10-15 00:47:04
# @Last Modified by:   Sergey Kuzmich
# @Last Modified time: 2017-10-15 23:18:14

echo "Deploying tag $TRAVIS_TAG"

export SVN_REPOSITORY_DIR="~/inline-spoilers-svn"

#  1. Clone complete SVN repository to separate directory
svn co $SVN_REPOSITORY $SVN_REPOSITORY_DIR

#  2. Copy git repository contents to SNV trunk/ directory
cp -R ./* $SVN_REPOSITORY_DIR/trunk/

#  3. Go to trunk/
cd $SVN_REPOSITORY_DIR/trunk/

#  4. Move assets/ to SVN /assets/
mv ./assets/ ../assets/

#  5. Delete .git/
rm -rf .git/

#  6. Delete deploy/
rm -rf deploy/

#  7. Delete .travis.yml
rm -rf .travis.yml

#  8. Delete README.md
rm -rf README.md

#  9. Create SVN tag directory
mkdir $SVN_REPOSITORY_DIR/tags/$TRAVIS_TAG

# 10. Copy trunk/ to tags/{tag}/
cp -R $SVN_REPOSITORY_DIR/trunk/* $SVN_REPOSITORY_DIR/tags/$TRAVIS_TAG

# 11. Commit SVN changes
svn ci --message "Release $TRAVIS_TAG" --username $SVN_USERNAME --password $SVN_PASSWORD --non-interactive

echo "Deployment of $TRAVIS_TAG is complete"
